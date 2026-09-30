from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route('/recycling-advice', methods=['POST'])
def recycling_advice():
    data = request.get_json()
    type_ = data.get('type', '').lower()
    category = data.get('category', '').lower()
    
    if 'plastic' in type_ or 'bottle' in category:
        advice = "Recycle in the blue bin ♻️"
    elif 'food' in category or 'organic' in type_:
        advice = "Can be composted 🍂"
    elif 'glass' in category:
        advice = "Recycle in the green bin 🟢"
    else:
        advice = "Check local recycling rules 🏠"
    
    return jsonify({'advice': advice})

if __name__ == '__main__':
    app.run(port=5001)
