from flask import Flask, request, jsonify
from transformers import pipeline
import os

app = Flask(__name__)

# On crée un pipeline text-generation avec un petit modèle gratuit
generator = pipeline("text-generation", model="gpt2")  # GPT-2 léger et gratuit

@app.route("/", methods=["GET"])
def home():
    return jsonify({
        "service": "Biodex Campaign Text Generator",
        "model": "gpt2",
        "endpoint": "GET /ask?prompt=your-text",
        "example": "/ask?prompt=Write%20a%20short%20campaign%20idea",
    })


@app.route('/ask', methods=['GET'])
def ask_ai():
    prompt = request.args.get('prompt', "Bonjour, écris-moi un haïku sur l'automne.")

    # Génère automatiquement la réponse
    result = generator(prompt, max_length=50, do_sample=True, temperature=0.7)
    answer = result[0]['generated_text']

    return jsonify({"answer": answer})

if __name__ == "__main__":
    debug = os.getenv("FLASK_DEBUG", "").strip().lower() in {"1", "true", "yes"}
    app.run(
        host=os.getenv("FLASK_HOST", "127.0.0.1"),
        port=int(os.getenv("CAMPAIGN_AI_PORT", "5003")),
        debug=debug,
        use_reloader=False,
    )
