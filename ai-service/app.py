from flask import Flask, request, jsonify
import math
import os

from collection_ai_service import CollectionAIService

app = Flask(__name__)
app.config['MAX_CONTENT_LENGTH'] = 2 * 1024 * 1024


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
    })


@app.route('/')
def home():
    return "✅ Service IA Biodex est en ligne 🧠"


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
