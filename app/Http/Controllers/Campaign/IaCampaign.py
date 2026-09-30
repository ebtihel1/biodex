from flask import Flask, request, jsonify
from transformers import pipeline

app = Flask(__name__)

# On crée un pipeline text-generation avec un petit modèle gratuit
generator = pipeline("text-generation", model="gpt2")  # GPT-2 léger et gratuit

@app.route('/ask', methods=['GET'])
def ask_ai():
    prompt = request.args.get('prompt', "Bonjour, écris-moi un haïku sur l'automne.")

    # Génère automatiquement la réponse
    result = generator(prompt, max_length=50, do_sample=True, temperature=0.7)
    answer = result[0]['generated_text']

    return jsonify({"answer": answer})

if __name__ == "__main__":
    app.run(debug=True)
