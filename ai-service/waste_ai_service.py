from flask import Flask, request, jsonify
import os

from ai_llm import OptionalWasteLLMFallback
from waste_classifier import classify_material

app = Flask(__name__)
app.config['MAX_CONTENT_LENGTH'] = 256 * 1024
llm_fallback = OptionalWasteLLMFallback()


@app.errorhandler(413)
def request_too_large(_error):
    del _error
    return jsonify({'error': 'Request body is too large.'}), 413


@app.route('/health', methods=['GET'])
def health_check():
    return jsonify({
        'status': 'healthy',
        'service': 'biodex-waste-advice',
        'version': '2.0.0',
        'llm_fallback': 'enabled' if llm_fallback.enabled else 'disabled',
    })

@app.route('/recycling-advice', methods=['POST'])
def recycling_advice():
    data = request.get_json(silent=True)
    if not isinstance(data, dict):
        return jsonify({'error': 'A JSON object is required.'}), 400

    for field in ('type', 'category', 'description'):
        value = data.get(field)
        if value is not None and not isinstance(value, str):
            return jsonify({'error': f'{field} must be text.'}), 400
        if value and len(value) > (2000 if field == 'description' else 120):
            return jsonify({'error': f'{field} exceeds the allowed length.'}), 400

    result = classify_material(
        data.get('type', ''),
        data.get('category', ''),
        data.get('description', ''),
        llm_fallback,
    )
    advice_by_category = {
        'plastic': 'Recycle plastic according to local resin and bin rules.',
        'paper': 'Recycle clean, dry paper and cardboard according to local rules.',
        'glass': 'Recycle glass separately; verify the local collection point.',
        'metal': 'Recycle metal separately according to local rules.',
        'organic': 'Compost if accepted by the local organic-waste program.',
        'electronic': 'Take electronics and batteries to a certified e-waste collection point.',
        'mixed': 'Separate the materials before recycling.',
        'unknown': 'Check local recycling rules or ask for a manual sorting review.',
    }
    return jsonify({
        'advice': advice_by_category[result['category']],
        **result,
    })

if __name__ == '__main__':
    debug = os.getenv('FLASK_DEBUG', '').strip().lower() in {'1', 'true', 'yes'}
    host = os.getenv('FLASK_HOST', '127.0.0.1')
    port = int(os.getenv('WASTE_AI_PORT', '5001'))
    if debug:
        app.run(host=host, port=port, debug=True)
    else:
        from waitress import serve
        serve(app, host=host, port=port, threads=int(os.getenv('AI_SERVICE_THREADS', '8')))
