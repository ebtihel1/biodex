from flask import Flask, request, jsonify
from datetime import datetime, timezone
import math
import os

from ai_llm import OptionalWasteLLMFallback
from waste_classifier import MATERIAL_ALIASES, classify_material, normalize_text

app = Flask(__name__)
app.config['MAX_CONTENT_LENGTH'] = 256 * 1024
llm_fallback = OptionalWasteLLMFallback()


@app.errorhandler(413)
def request_too_large(_error):
    del _error
    return jsonify({'success': False, 'error': 'Request body is too large.'}), 413


def _bounded_number(value, name, minimum, maximum=None):
    try:
        number = float(value)
    except (TypeError, ValueError) as error:
        raise ValueError(f'{name} must be a number.') from error
    if not math.isfinite(number) or number < minimum or (maximum is not None and number > maximum):
        raise ValueError(f'{name} is outside the allowed range.')
    return number

# ========================================
# 1. CLASSIFICATION DE DÉCHETS
# ========================================

@app.route('/classify-waste', methods=['POST'])
def classify_waste():
    """
    Classifie un déchet basé sur ses caractéristiques
    """
    try:
        data = request.get_json(silent=True)
        if not isinstance(data, dict):
            raise ValueError('A JSON object is required.')
        waste_type = data.get('type', '')
        category_hint = data.get('category', '')
        description = data.get('description', '')
        if any(not isinstance(value, str) for value in (waste_type, category_hint, description)):
            raise ValueError('type, category, and description must be text.')
        if len(waste_type) > 120 or len(category_hint) > 120 or len(description) > 2000:
            raise ValueError('Text fields exceed the allowed length.')
        weight = _bounded_number(data.get('weight', 0), 'weight', 0)

        classification = classify_waste_logic(waste_type, weight, description, category_hint, llm_fallback)
        
        return jsonify({
            'success': True,
            'classification': classification,
            'confidence': classification['confidence'],
            'recommended_method': classification['recommended_method']
        })
        
    except (TypeError, ValueError) as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 400
    except Exception:
        app.logger.exception('Waste classification failed')
        return jsonify({'success': False, 'error': 'Internal classification error.'}), 500

def classify_waste_logic(type_, weight, description, category_hint='', llm_fallback=None):
    result = classify_material(type_, category_hint, description, llm_fallback)
    category_labels = {
        'plastic': 'Plastique', 'glass': 'Verre', 'paper': 'Papier',
        'metal': 'Métal', 'organic': 'Organique', 'electronic': 'Électronique',
        'mixed': 'Mixte', 'unknown': 'Mixte',
    }
    methods = {
        'plastic': 'Recyclage mécanique après tri par résine',
        'glass': 'Tri, broyage et refonte du verre',
        'paper': 'Tri, pulpage et reformage',
        'metal': 'Tri magnétique ou par courant de Foucault, puis refonte',
        'organic': 'Compostage ou méthanisation selon la matière',
        'electronic': 'Collecte DEEE et démantèlement par une filière agréée',
        'mixed': 'Tri manuel recommandé avant recyclage',
        'unknown': 'Identifier la matière avant de choisir une filière',
    }
    category = result['category']
    notes = []
    if category == 'electronic':
        notes.append('Ne pas déposer avec les déchets ménagers; utiliser un point DEEE.')
    if category in {'mixed', 'unknown'}:
        notes.append('Plusieurs matières possibles ou matière non reconnue: demander un tri manuel.')
    if weight >= 25:
        notes.append('Prévoir une collecte adaptée au poids et au volume.')

    return {
        'category': category_labels[category],
        'confidence': result['confidence'],
        'confidence_type': result['confidence_type'],
        'classifier': result['classifier'],
        'confidence_type': result['confidence_type'],
        'recommended_method': methods[category],
        'recyclability_score': round(result['confidence'] * 100, 1),
        'signals': result['signals'],
        'alternatives': result['alternatives'],
        'needs_manual_sorting': result['needs_manual_sorting'],
        'handling_notes': notes,
    }


def _canonical_category(value):
    normalized = f' {normalize_text(value)} '
    category_labels = {
        'plastic': 'Plastique', 'glass': 'Verre', 'paper': 'Papier',
        'metal': 'Métal', 'organic': 'Organique', 'electronic': 'Électronique',
    }
    for category, aliases in MATERIAL_ALIASES.items():
        if any(f' {normalize_text(alias)} ' in normalized for alias in (category, *aliases)):
            return category_labels[category]
    return None


# ========================================
# 2. PRÉDICTION DE QUALITÉ
# ========================================

@app.route('/predict-quality', methods=['POST'])
def predict_quality():
    """
    Prédit la qualité du produit recyclé
    """
    try:
        data = request.get_json(silent=True)
        if not isinstance(data, dict):
            raise ValueError('A JSON object is required.')
        waste_type = data.get('waste_type', '')
        recycling_method = data.get('recycling_method', '')
        waste_condition = data.get('waste_condition', 'good')
        if any(not isinstance(value, str) for value in (waste_type, recycling_method, waste_condition)):
            raise ValueError('Waste type, method, and condition must be text.')
        storage_value = _bounded_number(data.get('storage_days', 0), 'storage_days', 0, 3650)
        if not storage_value.is_integer():
            raise ValueError('storage_days must be a whole number.')
        storage_days = int(storage_value)

        quality_prediction = predict_quality_logic(
            waste_type, recycling_method, waste_condition, storage_days
        )
        
        return jsonify({
            'success': True,
            'quality_prediction': quality_prediction
        })
        
    except (TypeError, ValueError) as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 400
    except Exception:
        app.logger.exception('Quality prediction failed')
        return jsonify({'success': False, 'error': 'Internal quality prediction error.'}), 500

def predict_quality_logic(waste_type, method, condition, storage_days):
    category = _canonical_category(waste_type)
    base_scores = {
        'Plastique': 78, 'Verre': 90, 'Papier': 74,
        'Métal': 84, 'Organique': 70, 'Électronique': 62,
    }
    condition_factors = {
        'excellent': 1.0, 'excellente': 1.0,
        'good': 0.9, 'bon': 0.9, 'bonne': 0.9,
        'fair': 0.72, 'average': 0.72, 'moyen': 0.72, 'moyenne': 0.72,
        'poor': 0.5, 'mauvais': 0.5, 'mauvaise': 0.5,
    }
    method_signals = {
        'Plastique': ('mechanical', 'mecanique'),
        'Verre': ('melting', 'molding', 'refonte', 'fusion'),
        'Papier': ('pulping', 'reforming', 'pulpage', 'reformage'),
        'Métal': ('melting', 'fusion', 'refonte'),
        'Organique': ('compost', 'methanisation', 'anaerobic'),
        'Électronique': ('disassembly', 'dismantling', 'demantelement', 'recovery'),
    }
    normalized_condition = normalize_text(condition)
    normalized_method = normalize_text(method)
    condition_factor = condition_factors.get(normalized_condition, 0.78)
    accepted_methods = method_signals.get(category, ())
    if not normalized_method:
        method_factor = 0.9
    elif any(signal in normalized_method for signal in accepted_methods):
        method_factor = 1.0
    else:
        method_factor = 0.75

    storage_rates = {
        'Plastique': 0.2, 'Verre': 0.05, 'Papier': 0.7,
        'Métal': 0.08, 'Organique': 1.5, 'Électronique': 0.15,
    }
    storage_penalty = min(storage_days * storage_rates.get(category, 0.5), 35)
    base_score = base_scores.get(category, 60)
    final_score = max(0, min(100, base_score * condition_factor * method_factor - storage_penalty))

    if final_score >= 85:
        quality_level = 'Excellent'
        color = 'success'
    elif final_score >= 70:
        quality_level = 'Bon'
        color = 'primary'
    elif final_score >= 50:
        quality_level = 'Moyen'
        color = 'warning'
    else:
        quality_level = 'Faible'
        color = 'danger'
    
    recommendations = get_quality_recommendations(final_score)
    if category == 'Organique' and storage_days > 3:
        recommendations.append('Contrôler les odeurs et la température; privilégier un traitement rapide.')
    if category == 'Électronique':
        recommendations.append('Vérifier le retrait des batteries et composants dangereux avant traitement.')

    return {
        'score': round(final_score, 1),
        'level': quality_level,
        'color': color,
        'confidence': round(
            0.35
            + (0.15 if category else 0)
            + (0.1 if normalized_condition in condition_factors else 0)
            + (0.1 if method_factor == 1.0 else 0),
            2,
        ),
        'confidence_type': 'input_completeness_not_calibrated',
        'material': category or 'Non déterminé',
        'prediction_type': 'heuristic',
        'factors': {
            'condition': round(condition_factor, 2),
            'method': round(method_factor, 2),
            'storage_penalty': round(storage_penalty, 1),
        },
        'recommendations': recommendations,
    }

def get_quality_recommendations(score):
    """
    Recommandations basées sur le score de qualité
    """
    if score >= 85:
        return ["Qualité excellente", "Idéal pour produits premium"]
    elif score >= 70:
        return ["Qualité correcte", "Convient pour usage standard"]
    elif score >= 50:
        return ["Qualité moyenne", "Considérer un prétraitement"]
    else:
        return ["Qualité faible", "Recyclage non recommandé", "Considérer la réutilisation"]

# ========================================
# 3. ESTIMATION DE PRIX
# ========================================

@app.route('/estimate-price', methods=['POST'])
def estimate_price():
    """
    Estime le prix optimal d'un produit recyclé
    """
    try:
        data = request.get_json(silent=True)
        if not isinstance(data, dict):
            raise ValueError('A JSON object is required.')
        product_name = data.get('product_name', 'Produit recyclé')
        waste_category = data.get('waste_category', '')
        if not isinstance(product_name, str) or len(product_name) > 120:
            raise ValueError('product_name must be text up to 120 characters.')
        if not isinstance(waste_category, str) or not waste_category.strip():
            raise ValueError('waste_category is required.')
        quality_score = _bounded_number(data.get('quality_score', 70), 'quality_score', 0, 100)
        recycling_cost = _bounded_number(data.get('recycling_cost', 10), 'recycling_cost', 0, 1_000_000)
        market_demand = _bounded_number(data.get('market_demand', 50), 'market_demand', 0, 100)

        price_estimation = estimate_price_logic(
            product_name, waste_category, quality_score, recycling_cost, market_demand
        )
        
        return jsonify({
            'success': True,
            'price_estimation': price_estimation
        })
        
    except (TypeError, ValueError) as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 400
    except Exception:
        app.logger.exception('Price estimation failed')
        return jsonify({'success': False, 'error': 'Internal price estimation error.'}), 500

def estimate_price_logic(product_name, category, quality, cost, demand):
    """Return an indicative heuristic price, not a live market quote."""
    base_prices = {
        'Plastique': 25,
        'Verre': 30,
        'Papier': 15,
        'Métal': 40,
        'Organique': 20,
        'Électronique': 60
    }
    
    canonical_category = _canonical_category(category)
    if canonical_category not in base_prices:
        raise ValueError('Unsupported waste_category.')
    base_price = base_prices[canonical_category]
    quality_multiplier = 0.35 + 0.65 * (quality / 100)
    demand_multiplier = 0.7 + 0.6 * (demand / 100)
    market_estimate = base_price * quality_multiplier * demand_multiplier
    final_price = max(market_estimate, cost * 1.3)
    profit_margin = max(0, final_price - cost)

    return {
        'product_name': product_name.strip()[:120],
        'category': canonical_category,
        'base_price': round(base_price, 2),
        'estimated_price': round(market_estimate, 2),
        'final_price': round(final_price, 2),
        'profit_margin': round(profit_margin, 2),
        'confidence': 0.35,
        'confidence_type': 'not_calibrated_no_market_data',
        'currency': 'DT',
        'estimate_type': 'rule_based_indicative',
        'disclaimer': 'Indicative estimate only; no live market prices or sales history were provided.',
        'price_range': [round(final_price * 0.8, 2), round(final_price * 1.2, 2)],
    }

# ========================================
# 4. GÉNÉRATION DE DESCRIPTIONS
# ========================================

@app.route('/generate-description', methods=['POST'])
def generate_description():
    """
    Génère une description marketing pour un produit recyclé
    """
    try:
        data = request.get_json(silent=True)
        if not isinstance(data, dict):
            raise ValueError('A JSON object is required.')
        product_name = data.get('product_name', 'Produit recyclé')
        source_material = data.get('source_material', '')
        recycling_method = data.get('recycling_method', '')
        specifications = data.get('specifications', {})
        if any(not isinstance(value, str) for value in (product_name, source_material, recycling_method)):
            raise ValueError('Product name, source material, and method must be text.')
        if len(product_name) > 120 or len(source_material) > 200 or len(recycling_method) > 200:
            raise ValueError('Description fields exceed the allowed length.')
        if not isinstance(specifications, dict) or len(specifications) > 20:
            raise ValueError('specifications must be an object with at most 20 entries.')
        for key, value in specifications.items():
            if not isinstance(key, str) or len(key) > 40 or not isinstance(value, (str, int, float, bool)):
                raise ValueError('Each specification must have a short text key and scalar value.')
            if isinstance(value, str) and len(value) > 200:
                raise ValueError('Specification values cannot exceed 200 characters.')

        description = generate_description_logic(
            product_name, source_material, recycling_method, specifications
        )
        
        return jsonify({
            'success': True,
            'description': description
        })
        
    except (TypeError, ValueError) as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 400
    except Exception:
        app.logger.exception('Product description generation failed')
        return jsonify({'success': False, 'error': 'Internal description generation error.'}), 500

def generate_description_logic(name, material, method, specs):
    category = _canonical_category(material)
    templates = {
        'Plastique': 'matière plastique recyclée',
        'Verre': 'verre recyclé',
        'Papier': 'papier ou carton recyclé',
        'Métal': 'métal recyclé',
        'Organique': 'matière organique valorisée',
        'Électronique': 'composants électroniques récupérés',
    }

    material_label = templates.get(category, material.strip() or 'matière recyclée')
    product_label = name.strip() or 'Produit recyclé'
    base_description = f'{product_label} est conçu à partir de {material_label}.'
    if category is None:
        base_description += ' La filière de valorisation dépend de la composition exacte du matériau.'

    if specs:
        specs_text = ', '.join(f'{key}: {value}' for key, value in specs.items())
        base_description += f' Spécifications déclarées: {specs_text}.'

    if method:
        base_description += f' Méthode déclarée: {method.strip()}.'

    return base_description[:5000]


@app.route('/optimize-process', methods=['POST'])
def optimize_process():
    try:
        data = request.get_json(silent=True)
        if not isinstance(data, dict):
            raise ValueError('A JSON object is required.')
        process_data = data.get('process_data', {})
        current_efficiency = _bounded_number(
            data.get('current_efficiency', 70), 'current_efficiency', 0, 100
        )
        result = optimize_process_logic(process_data, current_efficiency)
        return jsonify({'success': True, 'optimization': result})
    except (TypeError, ValueError) as error:
        return jsonify({'success': False, 'error': str(error)}), 400
    except Exception:
        app.logger.exception('Recycling process optimization failed')
        return jsonify({'success': False, 'error': 'Internal optimization error.'}), 500


def optimize_process_logic(process_data, current_efficiency):
    if isinstance(process_data, list):
        metrics = {}
        for item in process_data:
            if not isinstance(item, dict) or not isinstance(item.get('name'), str) or 'value' not in item:
                raise ValueError('process_data entries must include a name and value.')
            metrics[item['name']] = item['value']
    elif isinstance(process_data, dict):
        metrics = process_data
    else:
        raise ValueError('process_data must be an object or a list of named metrics.')

    metric_rules = (
        (('contamination_rate', 'contamination', 'taux de contamination'), 10, 'Reduce contamination by improving source separation and checking incoming batches.'),
        (('sorting_accuracy', 'sort_accuracy', 'sorting accuracy'), 85, 'Review sorting thresholds and retrain operators on the most confused material pairs.'),
        (('recovery_rate', 'material_recovery_rate'), 70, 'Audit losses at each processing stage and prioritize recovery of the largest residual stream.'),
    )
    recommendations = []
    observed_metrics = {}
    normalized_metrics = {normalize_text(key): value for key, value in metrics.items() if isinstance(key, str)}
    for aliases, threshold, recommendation in metric_rules:
        metric_name = next((normalize_text(alias) for alias in aliases if normalize_text(alias) in normalized_metrics), None)
        if metric_name is None:
            continue
        value = _bounded_number(normalized_metrics[metric_name], aliases[0], 0, 100)
        observed_metrics[aliases[0]] = value
        if (aliases[0] == 'contamination_rate' and value > threshold) or (
            aliases[0] != 'contamination_rate' and value < threshold
        ):
            recommendations.append({
                'priority': 'high' if abs(value - threshold) >= 15 else 'medium',
                'metric': aliases[0],
                'observed_value': round(value, 2),
                'recommendation': recommendation,
            })

    recommendations.sort(key=lambda item: (item['priority'] != 'high', item['metric']))
    if not recommendations:
        recommendations.append({
            'priority': 'data',
            'metric': None,
            'observed_value': None,
            'recommendation': 'Record contamination rate, sorting accuracy, and recovery rate before ranking process changes.',
        })

    return {
        'current_efficiency': round(current_efficiency, 1),
        'estimated_efficiency': None,
        'recommendations': recommendations,
        'observed_metrics': observed_metrics,
        'confidence': 0.65 if len(observed_metrics) >= 2 else 0.4,
        'optimization_type': 'rule_based',
        'disclaimer': 'No efficiency gain is claimed without measured before-and-after process data.',
    }

# ========================================
# 5. ROUTE DE SANTÉ
# ========================================

@app.route('/health', methods=['GET'])
def health_check():
    """
    Vérification de l'état du service
    """
    return jsonify({
        'status': 'healthy',
        'service': 'Biodex Recycling AI Service',
        'version': '2.0.0',
        'llm_fallback': 'enabled' if llm_fallback.enabled else 'disabled',
        'timestamp': datetime.now(timezone.utc).isoformat(),
    })

# ========================================
# 6. ROUTE D'ACCUEIL
# ========================================

@app.route('/', methods=['GET'])
def home():
    """
    Page d'accueil du service IA
    """
    return jsonify({
        'message': 'Biodex Recycling AI Service',
        'version': '2.0.0',
        'endpoints': [
            'POST /classify-waste - Classification des déchets',
            'POST /predict-quality - Prédiction de qualité',
            'POST /estimate-price - Estimation de prix',
            'POST /generate-description - Génération de descriptions',
            'POST /optimize-process - Recommendations from process metrics',
            'GET /health - État du service'
        ]
    })

if __name__ == '__main__':
    debug = os.getenv('FLASK_DEBUG', '').strip().lower() in {'1', 'true', 'yes'}
    host = os.getenv('FLASK_HOST', '127.0.0.1')
    port = int(os.getenv('RECYCLING_AI_PORT', '5002'))
    if debug:
        app.run(host=host, port=port, debug=True)
    else:
        from waitress import serve
        serve(app, host=host, port=port, threads=int(os.getenv('AI_SERVICE_THREADS', '8')))
