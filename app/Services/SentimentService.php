<?php

namespace App\Services;

use App\Models\Donation;
use Phpml\Classification\NaiveBayes;
use Exception;

class SentimentService
{
    private array $sampleData = [  // Extended training set for better accuracy
        // POSITIVE - Enthusiasm, satisfaction, gratitude
        'love this recycling program great condition' => 'positive',
        'excellent item perfect for reuse' => 'positive',
        'super excited to donate' => 'positive',
        'amazing recycling initiative so happy to participate' => 'positive',
        'wonderful condition barely used perfect for someone else' => 'positive',
        'fantastic program love helping the environment' => 'positive',
        'great quality item still works perfectly' => 'positive',
        'thrilled to contribute to recycling efforts' => 'positive',
        'beautiful item in perfect condition highly recommend' => 'positive',
        'so glad this program exists making a difference' => 'positive',
        'excellent condition like new ready for reuse' => 'positive',
        'happy to donate such a useful item' => 'positive',
        'proud to support this recycling initiative' => 'positive',
        'perfect item in amazing condition hope it helps' => 'positive',
        'love being part of this great community effort' => 'positive',
        'fantastic condition still has lots of life left' => 'positive',
        'wonderful experience donating this item' => 'positive',
        'great to see recycling programs working so well' => 'positive',
        'excellent quality hardly used ready for new home' => 'positive',
        'so pleased with this recycling program' => 'positive',

        // NEUTRAL - Factual descriptions, simple statements
        'standard plastic bottle used once' => 'neutral',
        'item as described' => 'neutral',
        'donating some paper' => 'neutral',
        'regular cardboard box clean and dry' => 'neutral',
        'typical glass bottle empty and rinsed' => 'neutral',
        'standard metal can no labels attached' => 'neutral',
        'ordinary plastic container previously used for food' => 'neutral',
        'basic electronic item working condition unknown' => 'neutral',
        'common household item no damage visible' => 'neutral',
        'standard furniture piece with normal wear' => 'neutral',
        'regular clothing item pre owned condition' => 'neutral',
        'typical book with some signs of use' => 'neutral',
        'standard plastic bag contains recyclable materials' => 'neutral',
        'ordinary paper product clean and dry' => 'neutral',
        'common glass item intact and clean' => 'neutral',
        'basic metal object no rust or damage' => 'neutral',
        'standard textile item washed and ready' => 'neutral',
        'regular wood item painted surface intact' => 'neutral',
        'typical rubber item flexible and clean' => 'neutral',
        'standard ceramic item no chips or cracks' => 'neutral',
        'ordinary fabric item minimal wear visible' => 'neutral',

        // NEGATIVE - Regret, frustration, poor condition
        'damaged and broken sorry' => 'negative',
        'not sure if recyclable waste' => 'negative',
        'bad shape dont want it' => 'negative',
        'completely broken beyond repair unfortunately' => 'negative',
        'terrible condition ripped and torn' => 'negative',
        'disappointed with the quality very worn out' => 'negative',
        'sadly damaged cannot be reused' => 'negative',
        'poor condition missing parts not functional' => 'negative',
        'heavily stained and dirty unfortunately' => 'negative',
        'cracked and shattered completely unusable' => 'negative',
        'very disappointed item in terrible shape' => 'negative',
        'regret this donation item is broken' => 'negative',
        'frustrating experience item not as expected' => 'negative',
        'unfortunately too damaged to be useful' => 'negative',
        'sadly this item is beyond recycling' => 'negative',
        'poor quality heavily worn not worth keeping' => 'negative',
        'terrible state completely ruined' => 'negative',
        'disappointing condition torn and stained' => 'negative',
        'broken pieces cannot be salvaged' => 'negative',
        'very poor condition regret donating this' => 'negative',
    ];

    private ?NaiveBayes $classifier = null;
    private array $vocabulary = [];

    // Basic stop words to filter out (common words that don't add sentiment value)
    private array $stopWords = [
        'the', 'a', 'an', 'is', 'are', 'was', 'were', 'be', 'been', 'being',
        'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could',
        'should', 'may', 'might', 'must', 'shall', 'can', 'to', 'of', 'in',
        'for', 'on', 'with', 'at', 'by', 'from', 'as', 'into', 'through',
        'during', 'before', 'after', 'above', 'below', 'between', 'under',
        'again', 'further', 'then', 'once', 'here', 'there', 'when', 'where',
        'why', 'how', 'all', 'each', 'few', 'more', 'most', 'other', 'some',
        'such', 'no', 'nor', 'not', 'only', 'own', 'same', 'so', 'than',
        'too', 'very', 'just', 'and', 'but', 'if', 'or', 'because', 'until',
        'while', 'this', 'that', 'these', 'those', 'it', 'its', 'i', 'me',
        'my', 'myself', 'we', 'our', 'ours', 'ourselves', 'you', 'your',
        'yours', 'yourself', 'yourselves', 'he', 'him', 'his', 'himself',
        'she', 'her', 'hers', 'herself', 'they', 'them', 'their', 'theirs',
    ];

    public function __construct()
    {
        try {
            $this->trainClassifier();
        } catch (Exception $e) {
            // Log the error for debugging (use your preferred logger, e.g., Laravel's Log facade)
            // \Log::error('Sentiment training failed: ' . $e->getMessage());
            $this->classifier = null; // Fallback to neutral predictions
        }
    }

    private function trainClassifier(): void
    {
        $samples = []; // tokenized
        $labels = [];

        foreach ($this->sampleData as $text => $label) {
            $tokens = $this->tokenizeAndClean($text);
            if (!empty($tokens)) {
                $samples[] = $tokens;
                $labels[] = $label;
            }
        }

        if (empty($samples)) {
            throw new Exception('No valid samples for sentiment training.');
        }

        // Build vocabulary
        $all_tokens = [];
        foreach ($samples as $sample_tokens) {
            $all_tokens = array_merge($all_tokens, $sample_tokens);
        }
        $this->vocabulary = array_unique($all_tokens);

        if (empty($this->vocabulary)) {
            throw new Exception('Vocabulary is empty after tokenization.');
        }

        // Transform to count vectors
        $transformedSamples = [];
        foreach ($samples as $tokens) {
            $counts = array_count_values($tokens);
            $vector = array_fill(0, count($this->vocabulary), 0);
            foreach ($this->vocabulary as $index => $token) {
                if (isset($counts[$token])) {
                    $vector[$index] = $counts[$token];
                }
            }
            $transformedSamples[] = $vector;
        }

        $this->classifier = new NaiveBayes();
        $this->classifier->train($transformedSamples, $labels);

        // Log training statistics for debugging
        \Log::info('Sentiment classifier trained successfully', [
            'total_samples' => count($samples),
            'vocabulary_size' => count($this->vocabulary),
            'positive_samples' => count(array_filter($labels, fn($l) => $l === 'positive')),
            'neutral_samples' => count(array_filter($labels, fn($l) => $l === 'neutral')),
            'negative_samples' => count(array_filter($labels, fn($l) => $l === 'negative')),
        ]);
    }

    private function tokenizeAndClean(string $text): array
    {
        // Remove punctuation and special characters, keep only letters and numbers
        $text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);

        // Split on whitespace and convert to lowercase
        $tokens = preg_split('/\s+/', strtolower(trim($text)));

        // Filter out empty tokens and stop words
        $tokens = array_filter($tokens, function($token) {
            return !empty($token) && !in_array($token, $this->stopWords);
        });

        return array_values($tokens); // Re-index array
    }

    public function analyzeSentiment(string $description): string
    {
        if (!$this->classifier || empty($description)) {
            return 'neutral';
        }

        $tokens = $this->tokenizeAndClean($description);

        if (empty($tokens)) {
            return 'neutral';
        }

        $counts = array_count_values($tokens);
        $vector = array_fill(0, count($this->vocabulary), 0);
        foreach ($this->vocabulary as $index => $token) {
            if (isset($counts[$token])) {
                $vector[$index] = $counts[$token];
            }
        }

        try {
            return $this->classifier->predict($vector);
        } catch (Exception $e) {
            // Fallback on prediction error (e.g., invalid vector)
            // \Log::warning('Sentiment prediction failed: ' . $e->getMessage());
            return 'neutral';
        }
    }

    // Batch for admin reports (e.g., in DonationController index)
    public function getSentimentsForDonations(array $donations): array
    {
        $sentiments = [];
        foreach ($donations as $donation) {
            $sentiments[$donation->id] = $this->analyzeSentiment($donation->description ?? '');
        }
        return $sentiments;
    }

    /**
     * Test the classifier with sample texts to verify accuracy
     * Useful for debugging and validating improvements
     */
    public function testClassifier(): array
    {
        $testCases = [
            'This is amazing and I love it!' => 'positive',
            'Great condition, perfect for reuse' => 'positive',
            'Standard item, no issues' => 'neutral',
            'It is what it is' => 'neutral',
            'Broken and damaged, very disappointed' => 'negative',
            'Terrible condition, cannot use' => 'negative',
        ];

        $results = [];
        $correct = 0;

        foreach ($testCases as $text => $expected) {
            $predicted = $this->analyzeSentiment($text);
            $isCorrect = $predicted === $expected;
            if ($isCorrect) {
                $correct++;
            }

            $results[] = [
                'text' => $text,
                'expected' => $expected,
                'predicted' => $predicted,
                'correct' => $isCorrect,
            ];
        }

        return [
            'accuracy' => ($correct / count($testCases)) * 100,
            'total_tests' => count($testCases),
            'correct_predictions' => $correct,
            'results' => $results,
        ];
    }
}