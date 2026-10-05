import re
import unicodedata


MATERIAL_ALIASES = {
    'glass': ('glass', 'verre', 'bocal', 'jar', 'vitre', 'glass bottle', 'bouteille en verre', 'flacon'),
    'paper': ('paper', 'papier', 'cardboard', 'carton', 'newspaper', 'journal', 'magazine'),
    'plastic': ('plastic', 'plastique', 'pet', 'hdpe', 'pehd', 'pvc', 'bouteille plastique', 'plastic bottle'),
    'organic': ('food', 'organic', 'organique', 'nourriture', 'dechet vert', 'food waste', 'restes alimentaires'),
    'metal': ('metal', 'metallique', 'aluminium', 'alu', 'steel', 'acier', 'canette', 'tin can'),
    'electronic': ('electronic', 'electronique', 'telephone', 'smartphone', 'ordinateur', 'computer', 'battery', 'batterie'),
}


def normalize_text(value):
    value = unicodedata.normalize('NFKD', str(value or '').casefold())
    value = ''.join(char for char in value if not unicodedata.combining(char))
    return re.sub(r'[^a-z0-9]+', ' ', value).strip()


def classify_material(type_text='', category_text='', description='', llm_fallback=None):
    """Classify by weighted, accent-insensitive phrase evidence; flag conflicts."""
    scores = {material: 0.0 for material in MATERIAL_ALIASES}
    evidence = {material: [] for material in MATERIAL_ALIASES}

    for source, value, weight in (
        ('type', type_text, 3.0),
        ('category', category_text, 4.0),
        ('description', description, 1.0),
    ):
        normalized = f' {normalize_text(value)} '
        for material, aliases in MATERIAL_ALIASES.items():
            for alias in aliases:
                normalized_alias = normalize_text(alias)
                if f' {normalized_alias} ' in normalized:
                    scores[material] += weight * min(1 + len(normalized_alias.split()) * 0.25, 1.75)
                    evidence[material].append({'source': source, 'term': normalized_alias})

    ranked = sorted(scores.items(), key=lambda item: item[1], reverse=True)
    category, score = ranked[0]
    second_score = ranked[1][1]
    alternatives = [name for name, value in ranked if value > 0 and name != category]
    ambiguous = score > 0 and second_score >= score * 0.75

    if score == 0:
        result = {
            'category': 'unknown',
            'confidence': 0.25,
            'confidence_type': 'heuristic_evidence_score_not_calibrated',
            'signals': [],
            'alternatives': [],
            'needs_manual_sorting': True,
        }
    elif ambiguous:
        result = {
            'category': 'mixed',
            'confidence': 0.45,
            'confidence_type': 'heuristic_evidence_score_not_calibrated',
            'signals': [signal for name, value in ranked if value > 0 for signal in evidence[name]],
            'alternatives': [name for name, value in ranked if value > 0],
            'needs_manual_sorting': True,
        }
    else:
        confidence = min(0.97, 0.55 + 0.4 * (score - second_score) / score)
        result = {
            'category': category,
            'confidence': round(confidence, 2),
            'confidence_type': 'heuristic_evidence_score_not_calibrated',
            'signals': evidence[category],
            'alternatives': alternatives,
            'needs_manual_sorting': False,
        }

    result['classifier'] = 'heuristic'
    if result['needs_manual_sorting'] and llm_fallback is not None:
        llm_category = llm_fallback.classify(type_text, category_text, description)
        if llm_category in MATERIAL_ALIASES:
            return {
                **result,
                'category': llm_category,
                'confidence': 0.5,
                'confidence_type': 'llm_label_not_calibrated',
                'needs_manual_sorting': False,
                'classifier': 'llm_fallback',
            }
    return result
