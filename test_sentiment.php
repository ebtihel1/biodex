<?php

require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\SentimentService;

echo "========================================\n";
echo "TEST DU SYSTÈME D'ANALYSE DE SENTIMENT\n";
echo "========================================\n\n";

$service = new SentimentService();

// Test basic predictions
echo "1. TESTS DE PRÉDICTION\n";
echo "----------------------------------------\n";

$testTexts = [
    "I love this recycling program! It's amazing and great for the environment.",
    "This item is in perfect condition, excellent quality!",
    "Standard plastic bottle, used once, clean and dry.",
    "Just a regular cardboard box, nothing special.",
    "Unfortunately the item is broken and damaged beyond repair.",
    "Terrible condition, very disappointed with the quality.",
];

foreach ($testTexts as $text) {
    $sentiment = $service->analyzeSentiment($text);
    echo "Text: \"$text\"\n";
    echo "Sentiment: " . strtoupper($sentiment) . "\n\n";
}

// Run classifier test
echo "\n2. TEST DE PRÉCISION DU CLASSIFICATEUR\n";
echo "----------------------------------------\n";

$results = $service->testClassifier();
echo "Précision: " . number_format($results['accuracy'], 2) . "%\n";
echo "Tests réussis: {$results['correct_predictions']}/{$results['total_tests']}\n\n";

echo "Détails des tests:\n";
foreach ($results['results'] as $result) {
    $status = $result['correct'] ? '✅' : '❌';
    echo "{$status} \"{$result['text']}\"\n";
    echo "   Attendu: {$result['expected']} | Prédit: {$result['predicted']}\n\n";
}

echo "========================================\n";
echo "FIN DES TESTS\n";
echo "========================================\n";
