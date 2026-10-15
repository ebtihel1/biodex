# Documentation Complète des Services IA - Projet Biodex

**Date**: 14 octobre 2026  
**Projet**: Biodex - Plateforme de Gestion de Déchets et Recyclage

---

## Table des Matières

1. [Vue d'Ensemble](#1-vue-densemble)
2. [Service 1: Classification d'Images de Déchets](#service-1-classification-dimages-de-déchets)
3. [Service 2: Prévisions de Collecte (Collection Point Prediction)](#service-2-prévisions-de-collecte-collection-point-prediction)
4. [Service 3: Analyse de Sentiment](#service-3-analyse-de-sentiment)
5. [Service 4: Recommandations de Produits](#service-4-recommandations-de-produits)
6. [Comparaison des Services IA](#6-comparaison-des-services-ia)

---

## 1. Vue d'Ensemble

### Architecture des Services IA

```
┌─────────────────────────────────────────────────────────────┐
│                    Application Laravel (PHP)                   │
├─────────────────────────────────────────────────────────────┤
│  Services PHP                                                │
│  ├── WasteImageClassifier.php      → Appel Service Flask    │
│  ├── CollectionAIController.php    → Appel Service Flask    │
│  ├── SentimentService.php          → Local (php-ml)          │
│  └── RecommendationService.php     → Local (php-ml)          │
└─────────────────────────────────────────────────────────────┘
                              ↓ HTTP
┌─────────────────────────────────────────────────────────────┐
│                Services Externes Flask (Python)                │
├─────────────────────────────────────────────────────────────┤
│  Port 5000: Collection Forecast (Time Series)               │
│  Port 5004: Image Classification (CNN)                        │
│  Port 5002: Recycling AI (Supprimé)                         │
│  Port 5003: Campaign AI (Supprimé)                            │
└─────────────────────────────────────────────────────────────┘
```

### Services Actifs

| Service | Type | Port | État | Utilité |
|---------|------|------|-------|---------|
| WasteImageClassifier | Flask (CNN) | 5004 | ✅ Actif | Classification d'images |
| CollectionAIController | Flask (Time Series) | 5000 | ✅ Actif | Prévisions de collecte |
| SentimentService | PHP (Naive Bayes) | - | ✅ Actif | Analyse de sentiment |
| RecommendationService | PHP (Apriori) | - | ✅ Actif | Recommandations produits |
| RecyclingAIController | Flask (ML) | 5002 | ❌ Supprimé | - |
| AIControllerCampaign | Flask (LLM) | 5003 | ❌ Supprimé | - |

---

## 2. Service 1: Classification d'Images de Déchets

### 2.1 Fichiers Impliqués

#### Fichiers PHP (Laravel)

1. **`app/Services/WasteImageClassifier.php`** (70 lignes)
   - Service principal de classification
   - Contient la logique d'appel au service Flask
   - Mapping des catégories (code → ID base de données)

2. **`app/Http/Controllers/AI/ImageClassificationController.php`** (34 lignes)
   - Controller HTTP pour l'API de classification
   - Gère le formulaire et l'upload d'image
   - Valide l'image avant classification

3. **`config/services.php`** (ligne 49-52)
   - Configuration de l'URL du service Flask
   - URL par défaut: `http://127.0.0.1:5004`

#### Fichiers Python (Flask - Service Externe)

⚠️ **Non inclus dans le code Laravel** - Service externe Python

```
Structure attendue du service Flask (port 5004):
- app.py (main application)
- model.py (modèle CNN)
- train.py (entraînement)
- requirements.txt (dépendances)
```

### 2.2 Fonctionnement

#### Flux de Classification

```
1. Utilisateur upload une image
        ↓
2. ImageClassificationController::classify()
        ↓
3. Validation de l'image (format, taille)
        ↓
4. WasteImageClassifier::classify()
        ↓
5. Envoi HTTP POST au service Flask (port 5004)
        ↓
6. Service Flask traite l'image avec CNN
        ↓
7. Retour: catégorie, confiance, top catégories
        ↓
8. Mapping code → ID catégorie (CATEGORY_BY_CODE)
        ↓
9. Retour JSON au frontend
```

#### Mapping des Catégories

```php
const CATEGORY_BY_CODE = [
    'plastic' => 1,      // Plastique
    'glass' => 2,        // Verre
    'paper' => 3,        // Papier
    'metal' => 4,        // Métal
    'organic' => 5,     // Organique
    'electronic' => 6,   // Électronique
    'textile' => 7,      // Textile
    'wood' => 8,         // Bois
];
```

### 2.3 Explication Technique

#### Communication HTTP

```php
$response = Http::timeout(30)
    ->attach('image', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
    ->post('http://127.0.0.1:5004/classify');
```

- **Timeout**: 30 secondes
- **Méthode**: POST multipart/form-data
- **Endpoint**: `/classify`

#### Réponse Attendue du Service Flask

```json
{
    "success": true,
    "category": "plastic",
    "category_label": "Plastique PET",
    "confidence": 0.92,
    "model": "mobilenet_v2",
    "top_categories": [
        {"category": "plastic", "confidence": 0.92},
        {"category": "paper", "confidence": 0.05},
        {"category": "glass", "confidence": 0.03}
    ]
}
```

#### Fallback (Si Service Indisponible)

```php
return [
    'category_code' => 'unknown',
    'category_label' => 'Inconnu',
    'confidence' => 0.0,
    'model' => 'service-unavailable',
    'source' => 'php_fallback',
    'top_categories' => [],
];
```

### 2.4 Routes

```php
// Dans routes/web.php
GET  /ai/classify        → Formulaire upload
POST /ai/classify        → Classification
```

### 2.5 Validation

```php
$request->validate([
    'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:6144'
]);
```

- **Formats acceptés**: JPEG, PNG, JPG, GIF, WEBP
- **Taille max**: 6MB
- **Obligatoire**: oui

### 2.6 Utilisation

#### Dans le Code PHP

```php
use App\Services\WasteImageClassifier;

$service = new WasteImageClassifier();
$result = $service->classify($uploadedFile);

// Résultat
[
    'category_code' => 'plastic',
    'category_label' => 'Plastique PET',
    'confidence' => 0.92,
    'model' => 'mobilenet_v2',
    'source' => 'python_cnn',
    'top_categories' => [...]
]
```

#### Obtenir l'ID de Catégorie

```php
$categoryId = $service->categoryIdForCode('plastic'); // Retourne 1
```

---

## 3. Service 2: Prévisions de Collecte (Collection Point Prediction)

### 3.1 Fichiers Impliqués

#### Fichiers PHP (Laravel)

1. **`app/Http/Controllers/AI/CollectionAIController.php`** (347 lignes)
   - Controller principal pour les prévisions
   - Gère l'entraînement et la prédiction
   - Système de fallback local

2. **`app/Models/CollectionPoint.php`**
   - Modèle des points de collecte
   - Relation avec wastes

3. **`app/Models/Waste.php`**
   - Modèle des déchets
   - Relation avec collection points

#### Fichiers Python (Flask - Service Externe)

⚠️ **Non inclus dans le code Laravel** - Service externe Python

```
Structure attendue du service Flask (port 5000):
- app.py (main application)
- model.py (modèle de prévision time series)
- train.py (entraînement)
- requirements.txt (dépendances)
```

### 3.2 Fonctionnement

#### Flux de Prédiction

```
1. Administrateur demande une prévision pour un point
        ↓
2. CollectionAIController::predict($id)
        ↓
3. Récupération de l'historique (90 derniers jours)
        ↓
4. Entraînement du modèle (train($id))
        ↓
5. Envoi HTTP POST au service Flask (port 5000)
        ↓
6. Service Flask entraîne le modèle avec les données
        ↓
7. Prédiction pour le lendemain
        ↓
8. Calcul du ratio vs capacité
        ↓
9. Détermination du statut (normal/almost_full/full)
        ↓
10. Retour JSON avec métriques détaillées
```

#### Méthodes Principales

##### `train($id)` - Entraînement

```php
// Récupère l'historique des 90 derniers jours
$records = Waste::select(
    DB::raw('DATE(created_at) as date'),
    DB::raw('SUM(weight) as volume')
)
->where('collection_point_id', $id)
->where('created_at', '>=', now()->subDays(89)->startOfDay())
->groupBy('date')
->orderBy('date')
->get();
```

**Données envoyées au service Flask**:
```json
{
    "point_id": 1,
    "data": [
        {"date": "2026-10-01", "volume": 450.5},
        {"date": "2026-10-02", "volume": 520.3},
        ...
    ]
}
```

**Validation**:
- Minimum 2 jours de données requis
- Sinon retourne erreur 422

##### `predict($id)` - Prédiction

```php
// Entraîne d'abord
$trainResponse = $this->train($id);

// Prédit pour le lendemain
$response = Http::timeout(10)->post('http://127.0.0.1:5000/collection/predict', [
    'date' => now()->addDay()->toDateString(),
    'point_id' => $id
]);
```

**Réponse attendue**:
```json
{
    "predicted_volume": 680.5,
    "confidence": 0.85,
    "lower_bound": 620.0,
    "upper_bound": 740.0,
    "model": "random_forest",
    "training_days": 45,
    "confidence_type": "bootstrap"
}
```

##### `forecasts()` - Prévisions pour tous les points

- Appelle `predict()` pour chaque point
- Optimisé pour carte interactive
- Retourne un tableau compact

##### `localForecast()` - Fallback Local

Si le service Flask est indisponible:

```php
// Calcule la moyenne des 14 derniers jours
$recent = array_slice($values, -14);
$prediction = array_sum($recent) / count($recent);

// Calcule l'écart-type
$variance = array_sum(array_map(fn ($volume) => ($volume - $prediction) ** 2, $recent)) / count($recent);
$spread = sqrt($variance);

// Confidence heuristique basée sur la couverture
$coverage = min(1.0, count($values) / 90);
$confidence = round(min(0.6, 0.25 + 0.35 * $coverage), 2);
```

### 3.3 Capacités des Points de Collecte

```php
private $capacities = [
    1 => 750,   // Centre de Collecte Tunis Nord
    2 => 600,   // Centre de Collecte Sousse
    3 => 900,   // Point Vert Les Berges du Lac
    4 => 850,   // Déchetterie Charguia
    5 => 650,   // Point de Collecte Nabeul
    6 => 1000,  // Centre de Tri Sfax
];
```

⚠️ **Problème**: Capacités codées en dur (devraient être en base de données)

### 3.4 Statuts de Remplissage

```php
private function statusFor(float $ratio): string
{
    if ($ratio >= 1) {
        return 'full';              // Plein (>= 100%)
    }
    if ($ratio >= 0.8) {
        return 'almost_full';       // Presque plein (80-99%)
    }
    return 'normal';                // Normal (< 80%)
}
```

### 3.5 Routes

```php
POST /collection/train/{id}        → Entraîner le modèle
POST /collection/predict/{id}       → Prédire le volume
GET  /collection/forecasts          → Prévisions tous points
```

### 3.6 Utilisation

#### Dashboard Administrateur

```php
// Dans le controller
$points = CollectionPoint::all();
$pointLocations = $points->mapWithKeys(fn ($p) => [
    $p->id => [
        'id' => $p->id,
        'name' => $p->name,
        'lat' => (float) $p->latitude,
        'lon' => (float) $p->longitude,
    ],
]);

return view('back.collectionpoints.predictions_dashboard', compact('collectionPoints', 'pointLocations'));
```

#### Appel API

```php
// Prédiction pour un point spécifique
$response = $collectionAIController->predict(1);
$data = $response->getData();

// Résultat
[
    'predicted_volume' => 680.5,
    'status' => 'almost_full',
    'capacity' => 750,
    'ratio' => 90.7,
    'confidence' => 0.85,
    'lower_bound' => 620.0,
    'upper_bound' => 740.0,
    ...
]
```

---

## 4. Service 3: Analyse de Sentiment

### 4.1 Fichiers Impliqués

#### Fichiers PHP (Laravel)

1. **`app/Services/SentimentService.php`** (132 lignes)
   - Service d'analyse de sentiment
   - Utilise Naive Bayes de php-ml
   - Fonctionne entièrement en local (pas de service externe)

### 4.2 Fonctionnement

#### Algorithme Naive Bayes

```
1. Initialisation
        ↓
2. Entraînement avec données d'exemple
        ↓
3. Tokenisation et nettoyage du texte
        ↓
4. Construction du vocabulaire
        ↓
5. Transformation en vecteurs de comptage
        ↓
6. Prédiction du sentiment
```

#### Données d'Entraînement

```php
private array $sampleData = [
    // Positive
    'love this recycling program great condition' => 'positive',
    'excellent item perfect for reuse' => 'positive',
    'super excited to donate' => 'positive',
    
    // Neutral
    'standard plastic bottle used once' => 'neutral',
    'item as described' => 'neutral',
    'donating some paper' => 'neutral',
    
    // Negative
    'damaged and broken sorry' => 'negative',
    'not sure if recyclable waste' => 'negative',
    'bad shape dont want it' => 'negative',
];
```

⚠️ **Problème**: Dataset très petit (9 exemples seulement)

#### Processus d'Entraînement

```php
// 1. Tokenisation
$tokens = preg_split('/\s+/', strtolower(trim($text)));

// 2. Construction du vocabulaire
$vocabulary = array_unique($all_tokens);

// 3. Transformation en vecteurs de comptage
$vector = array_fill(0, count($vocabulary), 0);
foreach ($vocabulary as $index => $token) {
    if (isset($counts[$token])) {
        $vector[$index] = $counts[$token];
    }
}

// 4. Entraînement Naive Bayes
$classifier = new NaiveBayes();
$classifier->train($transformedSamples, $labels);
```

#### Prédiction

```php
public function analyzeSentiment(string $description): string
{
    if (!$this->classifier || empty($description)) {
        return 'neutral';  // Fallback
    }

    // Tokenisation
    $tokens = $this->tokenizeAndClean($description);

    // Transformation en vecteur
    $vector = array_fill(0, count($this->vocabulary), 0);
    foreach ($this->vocabulary as $index => $token) {
        if (isset($counts[$token])) {
            $vector[$index] = $counts[$token];
        }
    }

    // Prédiction
    return $this->classifier->predict($vector);
}
```

### 4.3 Utilisation

#### Dans DonationController

```php
use App\Services\SentimentService;

$service = new SentimentService();
$sentiment = $service->analyzeSentiment($validated['description'] ?? '');

return redirect()->route($indexRoute)
    ->with('success', 'Donation created successfully! Sentiment: ' . ucfirst($sentiment));
```

#### Analyse en Batch

```php
// Pour les rapports administrateur
$sentiments = $service->getSentimentsForDonations($donations);

// Résultat
[
    1 => 'positive',
    2 => 'neutral',
    3 => 'negative',
    ...
]
```

### 4.4 Avantages et Inconvénients

#### Avantages
- ✅ Fonctionne entièrement en local (pas de dépendance externe)
- ✅ Rapide (pas d'appel HTTP)
- ✅ Simple à comprendre

#### Inconvénients
- ❌ Dataset d'entraînement très petit (9 exemples)
- ❌ Précision limitée
- ❌ Le vocabulaire est statique (pas de mise à jour)
- ❌ Gère mal les mots hors vocabulaire

---

## 5. Service 4: Recommandations de Produits

### 5.1 Fichiers Impliqués

#### Fichiers PHP (Laravel)

1. **`app/Services/RecommendationService.php`** (198 lignes)
   - Service de recommandations avec Apriori
   - Utilise php-ml (bibliothèque PHP)
   - Fonctionne entièrement en local

2. **`app/Services/RecommendationTester.php`** (331 lignes)
   - Outil de test du système de recommandation
   - Valide les résultats

3. **`app/Console/Commands/TestRecommendations.php`** (44 lignes)
   - Commande Artisan pour tester les recommandations

### 5.2 Fonctionnement

#### Algorithme Apriori

```
1. Construction des transactions
        ↓
2. Récupération de l'historique utilisateur
        ↓
3. Entraînement du modèle Apriori
        ↓
4. Prédiction basée sur l'historique
        ↓
5. Filtrage des produits déjà achetés
        ↓
6. Fallback vers produits populaires
```

#### Construction des Transactions

```php
private function buildTransactions(): array
{
    $transactions = [];

    // Récupérer toutes les commandes valides
    $orders = Order::where('status', '!=', 'cancelled')
        ->with('product')
        ->get()
        ->groupBy('user_id');

    foreach ($orders as $userId => $userOrders) {
        // Pour chaque utilisateur, créer une transaction avec tous ses produits
        $products = [];
        foreach ($userOrders as $order) {
            if ($order->product_id) {
                $products[] = $order->order->product_id;
            }
        }

        if (!empty($products)) {
            $transactions[] = array_unique($products);
        }
    }

    return $transactions;
}
```

**Exemple de transactions**:
```php
[
    [1, 5, 7],  // Utilisateur A a acheté les produits 1, 5, 7
    [2, 5, 9],  // Utilisateur B a acheté les produits 2, 5, 9
    [1, 3, 8],  // Utilisateur C a acheté les produits 1, 3, 8
]
```

#### Paramètres Apriori

```php
// Support dynamique selon le volume de données
$transactionCount = count($transactions);
$support = $transactionCount > 50 ? 0.1 : 0.05;  // 10% ou 5%
$confidence = 0.6;  // 60%
```

- **Support**: Fréquence minimale d'un itemset
- **Confidence**: Probabilité conditionnelle

#### Entraînement avec Cache

```php
private function getOrCreateAprioriModel(): Apriori
{
    $cacheKey = 'apriori_model_v2';
    $cacheDuration = 3600; // 1 heure

    return Cache::remember($cacheKey, $cacheDuration, function () {
        $transactions = $this->buildTransactions();
        $allProductIds = Product::where('is_available', true)->pluck('id')->toArray();
        
        $apriori = new Apriori($support, $confidence);
        $apriori->train($transactions, $allProductIds);
        
        return $apriori;
    });
}
```

#### Prédiction

```php
public function suggestForUser(User $user, int $limit = 5): array
{
    // 1. Récupérer l'historique
    $userProductIds = $this->getUserProductHistory($user);

    // 2. Si pas d'historique → produits populaires
    if (empty($userProductIds)) {
        return $this->getPopularProducts($limit);
    }

    // 3. Récupérer le modèle (avec cache)
    $apriori = $this->getOrCreateAprioriModel();

    // 4. Prédire
    $recommendations = $apriori->predict($userProductIds);

    // 5. Filtrer les produits déjà achetés
    $suggestions = [];
    foreach ($recommendations as $productId) {
        if (in_array($productId, $userProductIds)) {
            continue;  // Ignorer si déjà acheté
        }
        
        $product = Product::find($productId);
        if ($product && $product->is_available) {
            $suggestions[] = [...];
        }
    }

    // 6. Fallback si pas assez
    if (count($suggestions) < $limit) {
        $popularProducts = $this->getPopularProducts($limit - count($suggestions));
        $suggestions = array_merge($suggestions, $popularProducts);
    }

    return $suggestions;
}
```

#### Produits Populaires (Fallback)

```php
private function getPopularProducts(int $limit = 5): array
{
    return Cache::remember('popular_products', 1800, function () use ($limit) {
        return Product::select('id', 'name', 'price', 'image_path', 'stock_quantity')
            ->where('is_available', true)
            ->where('stock_quantity', '>', 0)
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image_path,
                    'stock' => $product->stock_quantity,
                ];
            })
            ->toArray();
    });
}
```

### 5.3 Invalidation du Cache

```php
public function invalidateCache(): void
{
    Cache::forget('apriori_model_v2');
    Cache::forget('popular_products');
}
```

**Appelé après**:
- Nouvelle commande
- Nouvelle réservation

### 5.4 Utilisation

#### Dans OrderController

```php
use App\Services\RecommendationService;

public function create(Request $request)
{
    // ...
    
    // Recommandations IA
    $recommendations = [];
    if (Auth::check()) {
        $service = new RecommendationService();
        $recommendations = $service->suggestForUser(Auth::user());
    }
    
    return view('orders.create', compact('products', 'recommendations'));
}
```

#### Test du Système

```bash
php artisan recommendations:test
```

---

## 6. Comparaison des Services IA

### Tableau Comparatif

| Service | Type | Port | Technologie | Dépendance | Données | Performance | Maintenance |
|---------|------|------|-------------|------------|---------|------------|--------------|
| **Image Classification** | Flask (CNN) | 5004 | Python (TensorFlow/Keras) | Externe | Images | Élevée | Élevée |
| **Collection Forecast** | Flask (Time Series) | 5000 | Python (scikit-learn) | Externe | Historique 90j | Élevée | Élevée |
| **Sentiment Analysis** | PHP (Naive Bayes) | - | php-ml | Aucune | 9 exemples | Moyenne | Faible |
| **Recommendations** | PHP (Apriori) | - | php-ml | Aucune | Commandes | Élevée | Faible |

### Avantages/Inconvénients

#### Services Externes (Flask)

**Avantages**:
- ✅ Performance élevée
- ✅ Algorithmes ML avancés
- ✅ Évolutifs (ré-entraînement)

**Inconvénients**:
- ❌ Dépendance externe
- ❌ Maintenance de services Python
- ❌ Besoin de serveur Python
- ❌ Complexité du déploiement

#### Services Locaux (PHP)

**Avantages**:
- ✅ Aucune dépendance externe
- ✅ Déploiement simple
- ✅ Maintenance facile
- ✅ Performance acceptable

**Inconvénients**:
- ❌ Algorithmes limités
- ❌ Performance moindre pour gros volumes
- ❌ Besoin de ré-entraînement manuel

---

## 7. Recommandations

### Services à Conserver

1. **Collection Forecast** (Port 5000)
   - Indispensable pour la gestion des points de collecte
   - Fallback robuste en cas de défaillance
   - Valeur métier claire

2. **Recommendations** (Apriori)
   - Améliore l'expérience utilisateur
   - Basé sur les vraies données
   - Fallback vers produits populaires

### Services à Améliorer

3. **Sentiment Analysis**
   - Augmenter le dataset d'entraînement
   - Ajouter des mots au vocabulaire dynamiquement
   - Utiliser un modèle plus avancé si possible

### Services à Évaluer

4. **Image Classification**
   - Évaluer l'utilisation réelle
   - Peut être remplacé par formulaire de sélection
   - Service externe complexe à maintenir

---

## 8. Résumé pour l'Utilisateur

### 1. Classification d'Images

**Fichiers**:
- `app/Services/WasteImageClassifier.php` (Service principal)
- `app/Http/Controllers/AI/ImageClassificationController.php` (Controller)
- `config/services.php` (Configuration)

**Fonctionnement**:
- Upload d'image → Envoi au service Flask (port 5004) → Classification CNN → Retour catégorie

**Explication IA**:
- Utilise un réseau de neurones convolutifs (CNN)
- Classifie les images en 8 catégories (plastique, verre, papier, etc.)
- Retourne un score de confiance
- Service externe Python non inclus dans le code Laravel

### 2. Prévisions de Collecte

**Fichiers**:
- `app/Http/Controllers/AI/CollectionAIController.php` (Controller principal)
- `app/Models/CollectionPoint.php` (Modèle)
- `app/Models/Waste.php` (Modèle)

**Fonctionnement**:
- Récupère l'historique des 90 derniers jours → Entraîne modèle Flask (port 5000) → Prédit volume du lendemain → Calcule ratio vs capacité

**Explication IA**:
- Utilise des algorithmes de séries temporelles (Random Forest, ARIMA, etc.)
- Entraîné sur les volumes quotidiens de collecte
- Prédit le volume pour J+1
- Service externe Python non inclus dans le code Laravel
- Fallback local utilisant la moyenne des 14 derniers jours

### 3. Autres IA

**Sentiment Analysis** (`app/Services/SentimentService.php`):
- Algorithme Naive Bayes (php-ml)
- Analyse le sentiment des textes (positive/negative/neutral)
- Fonctionne en local (pas de service externe)
- Dataset d'entraînement très petit (9 exemples)

**Recommendations** (`app/Services/RecommendationService.php`):
- Algorithme Apriori (php-ml)
- Recommande des produits basés sur l'historique d'achat
- Fonctionne en local (pas de service externe)
- Utilise les vraies commandes de la base de données
- Cache du modèle (1 heure)

---

**Document Version**: 1.0  
**Dernière Mise à Jour**: 14 octobre 2026
