"""
Biodex - Classification d'images de déchets (deep learning).

Endpoints :
    POST /classify   photo d'un déchet -> catégorie automatique
    GET  /health     état du service

Trois moteurs de classification, dans l'ordre de préférence :
    1. CNN MobileNetV2 / ImageNet  (TensorFlow)  - si TensorFlow est installé
    2. CNN MobileNetV2 / ImageNet  (ONNX Runtime) - si onnxruntime + modèle (.onnx) présent
    3. Heuristique PIL (couleur + texture) - repli léger sans modèle

Port par défaut : 5004 (variable d'environnement WASTE_IMG_PORT).
"""
import io
import json
import os

from flask import Flask, jsonify, request

app = Flask(__name__)
app.config['MAX_CONTENT_LENGTH'] = 6 * 1024 * 1024  # 6 Mo

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_DIR = os.path.join(BASE_DIR, 'models')
MOBILENET_ONNX = os.path.join(MODEL_DIR, 'mobilenetv2-7.onnx')
IMAGENET_INDEX = os.path.join(MODEL_DIR, 'imagenet_class_index.json')

CATEGORIES = [
    {'code': 'plastic', 'label': 'Plastique'},
    {'code': 'glass', 'label': 'Verre'},
    {'code': 'paper', 'label': 'Papier-Carton'},
    {'code': 'metal', 'label': 'Métal'},
    {'code': 'organic', 'label': 'Déchet organique'},
    {'code': 'electronic', 'label': 'Déchets électroniques'},
    {'code': 'textile', 'label': 'Textile'},
    {'code': 'wood', 'label': 'Bois'},
]

CATEGORY_BY_CODE = {c['code']: c for c in CATEGORIES}

# Mots-clés ImageNet -> catégorie Biodex (utilisés avec decode_predictions)
IMAGENET_KEYWORDS = {
    'plastic': ['plastic', 'bottle', 'pop bottle', 'soda bottle', 'water bottle', 'jar', 'bucket', 'tray', 'pack', 'bag', 'cup', 'canister', 'canister'],
    'glass': ['glass', 'wine bottle', 'beer bottle', 'jam jar', 'cocktail shaker', 'window'],
    'paper': ['paper', 'cardboard', 'carton', 'envelope', 'magazine', 'newspaper', 'book jacket', 'comic book', 'notebook', 'postcard', 'menu', 'packet', 'box'],
    'metal': ['can', 'tin', 'metal', 'hammer', 'nail', 'screw', 'padlock', 'chain', 'wrench', 'steel', 'iron', 'barrel', 'pan', 'wok', 'pot'],
    'organic': ['banana', 'apple', 'orange', 'fruit', 'vegetable', 'leaf', 'cucumber', 'mushroom', 'fungus', 'pinecone', 'strawberry', 'pizza', 'sandwich', 'salad', 'corn', 'plant', 'flower', 'rotten'],
    'electronic': ['mouse', 'keyboard', 'monitor', 'phone', 'cellular telephone', 'laptop', 'hard disc', 'printer', 'remote control', 'joystick', 'headphone', 'microphone', 'tape player', 'modem', 'router', 'screen'],
    'textile': ['jean', 'sweatshirt', 'sock', 'shirt', 'apron', 'bikini', 'gown', 'vestment', 'tiara', 'hat', 'shoe', 'sandal', 'bag', 'wool', 'fabric'],
    'wood': ['wooden spoon', 'abacus', 'spindle', 'paddle', 'potter', 'shoji', 'lintel', 'crate', 'log'],
}


class ImageClassifier:
    """Chargeurs paresseux pour ne dépendre d'aucune bibliothèque lourde au démarrage."""

    def __init__(self):
        self._tf_model = None
        self._tf_decode = None
        self._onnx_session = None
        self._onnx_labels = None

    # ------------------------------------------------------------------
    # Moteur CNN TensorFlow (MobileNetV2 / ImageNet)
    # ------------------------------------------------------------------
    def _load_tf(self):
        if self._tf_model is not None:
            return True
        try:
            import tensorflow as tf
            from tensorflow.keras.applications import MobileNetV2
            from tensorflow.keras.applications.mobilenet_v2 import decode_predictions, preprocess_input
            from tensorflow.keras.preprocessing.image import img_to_array

            self._tf_model = MobileNetV2(weights='imagenet', include_top=True)
            self._tf_img_to_array = img_to_array
            self._tf_preprocess = preprocess_input
            self._tf_decode = decode_predictions
            return True
        except Exception as exc:
            print(f'[image_classifier] TensorFlow indisponible : {exc}')
            return False

    # ------------------------------------------------------------------
    # Moteur CNN ONNX Runtime (MobileNetV2 / ImageNet)
    # ------------------------------------------------------------------
    def _load_onnx(self):
        if self._onnx_session is not None:
            return True
        if not (os.path.exists(MOBILENET_ONNX) and os.path.exists(IMAGENET_INDEX)):
            return False
        try:
            import onnxruntime as ort
            import numpy as np  # noqa: F401

            session = ort.InferenceSession(MOBILENET_ONNX, providers=['CPUExecutionProvider'])
            with open(IMAGENET_INDEX, encoding='utf-8') as fh:
                index = json.load(fh)
            labels = [index[str(i)][1] for i in range(len(index))]
            if len(labels) != 1000:
                raise ValueError(f'Index ImageNet inattendu : {len(labels)} classes')
            self._onnx_session = session
            self._onnx_labels = labels
            return True
        except Exception as exc:
            self._onnx_session = None
            print(f'[image_classifier] ONNX indisponible : {exc}')
            return False

    def is_cnn_ready(self):
        return self._load_tf() or self._load_onnx()

    def classify(self, image_bytes):
        """Classifie une image et retourne l'objet de réponse (dict)."""
        if self._load_tf():
            try:
                return self._classify_cnn_tf(image_bytes)
            except Exception as exc:
                print(f'[image_classifier] Échec CNN TF, bascule ONNX : {exc}')

        if self._load_onnx():
            try:
                return self._classify_cnn_onnx(image_bytes)
            except Exception as exc:
                print(f'[image_classifier] Échec CNN ONNX, repli heuristique : {exc}')

        return self._classify_heuristic(image_bytes)

    @staticmethod
    def _aggregate_scores(top_predictions):
        """Répartit les probabilités ImageNet (label, prob) sur les catégories Biodex."""
        scores = {code: 0.0 for code in CATEGORY_BY_CODE}
        for (label, prob) in top_predictions:
            label_lower = label.lower()
            for code, keywords in IMAGENET_KEYWORDS.items():
                if any(kw in label_lower for kw in keywords):
                    scores[code] += float(prob)

        ranked = sorted(scores.items(), key=lambda item: item[1], reverse=True)
        total = sum(score for _, score in ranked) or 1.0
        top_categories = [
            {'code': code, 'label': CATEGORY_BY_CODE[code]['label'], 'confidence': round(score / total, 4)}
            for code, score in ranked
        ]

        top_code, top_score = ranked[0]
        if top_score <= 0.0:
            return 'unknown', 'Inconnu', 0.0, top_categories

        label = CATEGORY_BY_CODE[top_code]['label']
        confidence = round(min(0.99, top_score / total), 4)
        return top_code, label, confidence, top_categories

    # ------------------------------------------------------------------
    # Classification CNN via TensorFlow
    # ------------------------------------------------------------------
    def _classify_cnn_tf(self, image_bytes):
        import numpy as np

        from PIL import Image

        image = Image.open(io.BytesIO(image_bytes)).convert('RGB').resize((224, 224))
        preprocessed = self._tf_preprocess(np.expand_dims(self._tf_img_to_array(image), axis=0))
        predictions = self._tf_model.predict(preprocessed, verbose=0)
        top = self._tf_decode(predictions, top=5)[0]
        top_predictions = [(label, float(prob)) for (_wnid, label, prob) in top]
        code, label, confidence, top_categories = self._aggregate_scores(top_predictions)

        return {
            'success': True,
            'category': code,
            'category_label': label,
            'confidence': confidence,
            'model': 'mobilenetv2-imagenet',
            'top_categories': top_categories,
        }

    # ------------------------------------------------------------------
    # Classification CNN via ONNX Runtime
    # ------------------------------------------------------------------
    def _classify_cnn_onnx(self, image_bytes):
        import numpy as np

        from PIL import Image

        image = Image.open(io.BytesIO(image_bytes)).convert('RGB').resize((224, 224), Image.BILINEAR)
        x = (np.asarray(image, dtype=np.float32) / 127.5) - 1.0
        x = np.expand_dims(x, 0)  # NHWC [1, 224, 224, 3]

        session = self._onnx_session
        input_shape = session.get_inputs()[0].shape
        if len(input_shape) == 4 and input_shape[1] == 3:
            x = np.transpose(x, (0, 3, 1, 2))  # modèle NCHW [1, 3, 224, 224]

        probs = session.run(None, {session.get_inputs()[0].name: x})[0][0]
        if float(probs.max()) > 1.001 or float(probs.min()) < -0.001:
            probs = np.exp(probs - probs.max())
            probs /= probs.sum()

        top_idx = np.argsort(probs)[-5:][::-1]
        top_predictions = [(self._onnx_labels[int(i)], float(probs[i])) for i in top_idx]
        code, label, confidence, top_categories = self._aggregate_scores(top_predictions)

        return {
            'success': True,
            'category': code,
            'category_label': label,
            'confidence': confidence,
            'model': 'mobilenetv2-onnx',
            'top_categories': top_categories,
        }

    # ------------------------------------------------------------------
    # Repli heuristique PIL (sans modèle)
    # ------------------------------------------------------------------
    def _classify_heuristic(self, image_bytes):
        from PIL import Image, ImageStat

        image = Image.open(io.BytesIO(image_bytes)).convert('RGB')
        small = image.resize((64, 64))

        stat = ImageStat.Stat(small)
        mean_rgb = stat.mean
        std_rgb = stat.stddev
        brightness = sum(mean_rgb) / 3.0
        saturation = (max(mean_rgb) - min(mean_rgb)) / max(1.0, brightness)
        gray_std = ImageStat.Stat(small.convert('L')).stddev[0]

        # Niveau de brillance : pixels très lumineux voisins de zones sombres
        # (reflets spéculaires d'une surface lisse plastique / verre).
        luminance = small.convert('L')
        px = list(luminance.getdata())
        n = len(px)
        bright_ratio = sum(1 for v in px if v > 235) / max(1, n)
        glossy = bright_ratio > 0.015

        r, g, b = mean_rgb
        greenish = g > r and g > b
        brownish = r > 90 and g > 60 and b < 90 and r > b

        name, reason = None, ''
        if greenish and saturation > 0.25:
            name, reason = 'organic', 'dominante verte organique'
        elif brownish and gray_std > 12:
            name, reason = 'wood', 'tons bois'
        elif brightness > 205 and saturation < 0.18:
            # Objet clair et peu saturé : plastique lisse (reflets) vs papier mat.
            if glossy:
                name, reason = 'plastic', 'surface claire et brillante (plastique)'
            elif gray_std > 26:
                name, reason = 'paper', 'surface claire et fibreuse (papier)'
            else:
                name, reason = 'plastic', 'surface claire teintée (plastique probable)'
        elif contrast := (sum(std_rgb) / 3.0) > 100 and saturation < 0.2 and brightness < 210:
            name, reason = 'metal', 'surface réfléchissante'
        elif greenish or (saturation > 0.35):
            name, reason = 'organic', 'couleur vive organique'
        elif gray_std < 14 and not glossy:
            name, reason = 'paper', 'surface uniforme et mate'
        elif glossy and brightness > 140:
            name, reason = 'glass', 'surface transparente brillante'
        else:
            name, reason = 'plastic', 'surface uniforme (plastique probable)'

        distribution = {
            'organic': 0.40, 'paper': 0.28, 'plastic': 0.16, 'metal': 0.08,
            'glass': 0.04, 'wood': 0.02, 'electronic': 0.01, 'textile': 0.01,
        }
        if name != 'unknown':
            # On met la catégorie détectée en tête de la distribution pour
            # que la barre de confiance corresponde à la décision affichée.
            distribution[name] = max(distribution.values()) + 0.25
        total = sum(distribution.values())
        ranked = sorted(distribution.items(), key=lambda item: item[1], reverse=True)
        top_categories = [
            {'code': code, 'label': CATEGORY_BY_CODE[code]['label'], 'confidence': round(prob / total, 4)}
            for code, prob in ranked
        ]

        if name == 'unknown':
            name = ranked[0][0]
            confidence = 0.35
            label = CATEGORY_BY_CODE[name]['label']
        else:
            confidence = round(min(0.85, 0.45 + bright_ratio * 2.0 + gray_std / 500.0), 4)
            label = CATEGORY_BY_CODE.get(name, {}).get('label', 'Inconnu')

        return {
            'success': True,
            'category': name,
            'category_label': label,
            'confidence': confidence,
            'model': 'heuristic-pil',
            'reason': reason,
            'top_categories': top_categories,
        }


classifier = ImageClassifier()


@app.errorhandler(413)
def request_too_large(_error):
    del _error
    return jsonify({'error': 'Request body is too large.'}), 413


@app.errorhandler(400)
def bad_request(_error):
    del _error
    return jsonify({'error': 'An image file is required (field name: image).'}), 400


@app.route('/health', methods=['GET'])
def health_check():
    if classifier._load_tf():
        engine = 'mobilenetv2-imagenet'
    elif classifier._load_onnx():
        engine = 'mobilenetv2-onnx'
    else:
        engine = 'heuristic-pil'
    return jsonify({
        'status': 'healthy',
        'service': 'biodex-image-classifier',
        'engine': engine,
    })


@app.route('/classify', methods=['POST'])
def classify():
    if 'image' in request.files:
        file = request.files['image']
        if not file or not file.filename:
            return jsonify({'error': 'An image file is required (field name: image).'}), 400
        data = file.read()
    elif request.is_json:
        payload = request.get_json(silent=True) or {}
        raw = payload.get('image')
        if not raw:
            return jsonify({'error': 'Provide an image base64 in the JSON body.'}), 400
        import base64
        try:
            data = base64.b64decode(raw)
        except Exception:
            return jsonify({'error': 'Invalid base64 image payload.'}), 400
    else:
        return jsonify({'error': 'An image file is required (field name: image).'}), 400

    if not data:
        return jsonify({'error': 'Empty image payload.'}), 400

    try:
        from PIL import Image
        Image.open(io.BytesIO(data)).verify()
    except Exception:
        return jsonify({'error': 'The uploaded file is not a valid image.'}), 422

    return jsonify(classifier.classify(data))


if __name__ == '__main__':
    debug = os.getenv('FLASK_DEBUG', '').strip().lower() in {'1', 'true', 'yes'}
    host = os.getenv('FLASK_HOST', '127.0.0.1')
    port = int(os.getenv('WASTE_IMG_PORT', '5004'))
    if debug:
        app.run(host=host, port=port, debug=True)
    else:
        from waitress import serve
        serve(app, host=host, port=port, threads=int(os.getenv('AI_SERVICE_THREADS', '8')))