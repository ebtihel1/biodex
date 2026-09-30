<?php

namespace App\Services;

use App\Models\Donation;
use Phpml\Classification\NaiveBayes;
use Exception;

class SentimentService
{
    private array $sampleData = [  // Tiny training set (cleaned, no punctuation/symbols)
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

    private ?NaiveBayes $classifier = null;
    private array $vocabulary = [];

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
    }

    private function tokenizeAndClean(string $text): array
    {
        // Simple split on whitespace, lowercase
        $tokens = preg_split('/\s+/', strtolower(trim($text)));
        return array_filter($tokens, function($token) {
            return !empty($token);
        });
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
}