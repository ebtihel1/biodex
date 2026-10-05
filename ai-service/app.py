from flask import Flask, request, jsonify
import math
import os

from collection_ai_service import CollectionAIService
from ai_llm import OptionalWasteLLMFallback
from waste_classifier import classify_material

app = Flask(__name__)
app.config['MAX_CONTENT_LENGTH'] = 2 * 1024 * 1024
llm_fallback = OptionalWasteLLMFallback()


@app.errorhandler(413)
def request_too_large(_error):
    del _error
    return jsonify({'error': 'Request body is too large.'}), 413


@app.route('/health', methods=['GET'])
def health_check():
    return jsonify({
        'status': 'healthy',
        'service': 'biodex-ai',
        'version': '2.0.0',
        'llm_fallback': 'enabled' if llm_fallback.enabled else 'disabled',
    })

# =========================================================
# 🔹 SERVICE 1 : IA DE CLASSEMENT DES DÉCHETS
# =========================================================
@app.route('/')
def home():
    return "✅ Service IA Biodex est en ligne 🧠"

@app.route('/predict', methods=['POST'])
def predict_waste():
    """
    Prédit l'action à effectuer selon le type de déchet :
    recycler, composter, réutiliser ou donner
    weight est en kg
    """
    data = request.get_json(silent=True)
    if not isinstance(data, dict):
        return jsonify({'error': 'An object JSON body is required.'}), 400

    try:
        weight = float(data.get('weight') or 0)
    except (TypeError, ValueError):
        return jsonify({'error': 'weight must be a number in kilograms.'}), 400
    if not math.isfinite(weight) or weight < 0:
        return jsonify({'error': 'weight must be a non-negative number.'}), 400

    text_fields = ('type', 'category', 'description')
    if any(data.get(key) is not None and not isinstance(data.get(key), str) for key in text_fields):
        return jsonify({'error': 'type, category, and description must be text.'}), 400

    type_text = (data.get('type') or '').strip()
    category_text = (data.get('category') or '').strip()
    description = (data.get('description') or '').strip()
    if len(type_text) > 120 or len(category_text) > 120 or len(description) > 2000:
        return jsonify({'error': 'Text fields exceed the allowed length.'}), 400
    result = classify_material(type_text, category_text, description, llm_fallback)
    material = result['category']
    if material == 'unknown':
        prediction = 'Sort manually: material not recognized'
        notes = ['Add a clearer material type or description for a better recommendation.']
    elif material == 'mixed':
        prediction = 'Sort materials before recycling'
        notes = ['Several materials were detected; separate them before disposal.']
    else:
        prediction = WASTE_GUIDANCE[material]['action']
        notes = list(WASTE_GUIDANCE[material]['notes'])

    if weight >= 25:
        notes.append('Arrange a collection method suitable for the reported weight.')

    return jsonify({
        'success': True,
        'prediction': prediction,
        'category': material,
        'confidence': result['confidence'],
        'signals': result['signals'],
        'classifier': result['classifier'],
        'confidence_type': result['confidence_type'],
        'alternatives': result['alternatives'],
        'needs_manual_sorting': result['needs_manual_sorting'],
        'handling_notes': notes,
    })


WASTE_GUIDANCE = {
    'glass': {
        'action': 'Recycle as glass using local collection rules',
        'notes': ['Remove lids and non-glass parts where possible.'],
    },
    'paper': {
        'action': 'Recycle paper/cardboard using local collection rules',
        'notes': ['Keep paper and cardboard dry and clean.'],
    },
    'plastic': {
        'action': 'Recycle plastic using local resin and collection rules',
        'notes': ['Check the local resin rules and rinse containers when required.'],
    },
    'organic': {
        'action': 'Compost',
        'notes': ['Use the local composting rules; meat and liquids may be excluded.'],
    },
    'metal': {
        'action': 'Recycle as metal',
        'notes': ['Separate metal from plastic or paper components when practical.'],
    },
    'electronic': {
        'action': 'Take to a certified e-waste collection point',
        'notes': ['Do not place batteries or electronics in household recycling bins.'],
    },
}

collection_ai = CollectionAIService()

# =========================================================
# 🔹 ROUTES FLASK POUR LES POINTS DE COLLECTE
# =========================================================
@app.route('/collection/train', methods=['POST'])
def train_collection():
    try:
        payload = request.get_json(silent=True)
        if not isinstance(payload, dict):
            return jsonify({"error": "Un objet JSON est requis."}), 400
        point_id = payload['point_id']
        data = payload['data']
        result = collection_ai.train_model(point_id, data)
        return jsonify({
            "message": "✅ Modèle entraîné avec succès",
            "details": result
        })
    except KeyError as e:
        return jsonify({"error": f"Champ manquant: {str(e)}"}), 400
    except (TypeError, ValueError) as e:
        return jsonify({"error": str(e)}), 400
    except Exception:
        app.logger.exception('Collection model training failed')
        return jsonify({"error": "Erreur interne pendant l’entraînement."}), 500

@app.route('/collection/predict', methods=['POST'])
def predict_collection():
    try:
        payload = request.get_json(silent=True)
        if not isinstance(payload, dict):
            return jsonify({"error": "Un objet JSON est requis."}), 400
        point_id = payload['point_id']
        forecast_date = payload.get('date')
        day = payload.get('day')
        if not forecast_date and day is None:
            return jsonify({"error": "Champ manquant: date ou day"}), 400
        forecast = collection_ai.predict(point_id, target_date=forecast_date, day=day)
        return jsonify({**forecast, "date": forecast_date, "day": day})
    except KeyError as e:
        return jsonify({"error": f"Champ manquant: {str(e)}"}), 400
    except (TypeError, ValueError) as e:
        return jsonify({"error": str(e)}), 400
    except Exception:
        app.logger.exception('Collection volume prediction failed')
        return jsonify({"error": "Erreur interne pendant la prédiction."}), 500

if __name__ == '__main__':
    debug = os.getenv('FLASK_DEBUG', '').strip().lower() in {'1', 'true', 'yes'}
    host = os.getenv('FLASK_HOST', '127.0.0.1')
    port = int(os.getenv('FLASK_PORT', '5000'))
    if debug:
        app.run(host=host, port=port, debug=True)
    else:
        from waitress import serve
        serve(app, host=host, port=port, threads=int(os.getenv('AI_SERVICE_THREADS', '8')))
