<?php

namespace App\Services;

use App\Models\User;
use Phpml\Association\Apriori;

class RecommendationService
{
    private array $products = [  // Reuse your hardcoded ones
        1 => ['name' => 'Product ATT', 'price' => 10.00, 'related_waste' => 'plastic'],  // Add 'related_waste' for eco-links
        2 => ['name' => 'Product ATTC', 'price' => 15.00, 'related_waste' => 'paper'],
        3 => ['name' => 'Product ATTR', 'price' => 20.00, 'related_waste' => 'glass'],
    ];

    public function suggestForUser(User $user, int $limit = 3): array
    {
        // Get user history: donations (waste_ids) + past orders/reservations (product_ids)
        $pastWaste = $user->donations->pluck('waste_id')->toArray();
        $pastProducts = $user->orders->pluck('product_id')->merge($user->reservations->pluck('product_id'))->toArray();

        if (empty($pastWaste) && empty($pastProducts)) {
            // Fallback: Slice to numeric indices (0,1,2)
            return array_slice(array_values($this->products), 0, $limit, true);
        }

        // Train simple Apriori on history (associations like waste1 → product2)
        $transactions = $this->buildTransactions($pastWaste, $pastProducts);
        $apriori = new Apriori(0.5, 0.5);  // Support/confidence thresholds
        $apriori->train($transactions, array_keys($this->products));  // Items: product IDs

        // Predict next
        $recommendations = $apriori->predict($pastProducts ?: [1]);  // Seed with dummy if empty
        $suggestions = [];
        foreach ($recommendations as $productId) {
            if (isset($this->products[$productId])) {
                $suggestions[] = $this->products[$productId];  // Numeric push
                if (count($suggestions) >= $limit) break;
            }
        }

        // Ensure numeric if needed (fallback)
        if (empty($suggestions)) {
            $suggestions = array_slice(array_values($this->products), 0, $limit, true);
        }

        return $suggestions;
    }

    private function buildTransactions(array $wasteIds, array $productIds): array
    {
        // Simple: Map waste to potential products (expand with your logic)
        $transactions = [];
        foreach (array_merge($wasteIds, $productIds) as $item) {
            $transactions[] = [$item];  // Each as a "basket"
        }
        return $transactions;
    }
}