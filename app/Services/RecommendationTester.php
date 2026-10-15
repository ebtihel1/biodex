<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecommendationTester
{
    /**
     * Teste le système de recommandation et affiche les résultats
     */
    public function test(): array
    {
        $results = [];

        // 1. Vérifier les données disponibles
        $results['data_stats'] = $this->getDataStatistics();

        // 2. Tester avec un utilisateur sans historique
        $results['new_user_test'] = $this->testNewUser();

        // 3. Tester avec un utilisateur avec historique
        $results['existing_user_test'] = $this->testExistingUser();

        // 4. Vérifier les règles d'association
        $results['association_rules'] = $this->getAssociationRules();

        // 5. Tester la cohérence des recommandations
        $results['coherence_test'] = $this->testCoherence();

        return $results;
    }

    /**
     * Statistiques des données disponibles
     */
    private function getDataStatistics(): array
    {
        return [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'available_products' => Product::where('is_available', true)->count(),
            'total_orders' => Order::count(),
            'valid_orders' => Order::where('status', '!=', 'cancelled')->count(),
            'orders_with_products' => Order::whereNotNull('product_id')->count(),
            'users_with_orders' => Order::distinct('user_id')->count('user_id'),
        ];
    }

    /**
     * Test avec un utilisateur sans historique
     */
    private function testNewUser(): array
    {
        // Trouver un utilisateur sans commandes
        $user = User::whereDoesntHave('orders')
            ->whereDoesntHave('reservations')
            ->first();

        if (!$user) {
            return [
                'status' => 'skipped',
                'message' => 'Aucun utilisateur sans historique trouvé',
            ];
        }

        $service = new RecommendationService();
        $recommendations = $service->suggestForUser($user, 5);

        return [
            'status' => 'success',
            'user_id' => $user->id,
            'user_name' => $user->name,
            'has_history' => false,
            'recommendations_count' => count($recommendations),
            'recommendations' => $recommendations,
            'expected' => 'Products populaires (fallback)',
            'is_popular' => $this->verifyPopularProducts($recommendations),
        ];
    }

    /**
     * Test avec un utilisateur avec historique
     */
    private function testExistingUser(): array
    {
        // Trouver un utilisateur avec au moins 2 commandes
        $user = User::whereHas('orders', function ($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->with('orders.product')
            ->first();

        if (!$user) {
            return [
                'status' => 'skipped',
                'message' => 'Aucun utilisateur avec historique trouvé',
            ];
        }

        $service = new RecommendationService();
        $recommendations = $service->suggestForUser($user, 5);

        $userProductIds = $service->getUserProductHistory($user);

        return [
            'status' => 'success',
            'user_id' => $user->id,
            'user_name' => $user->name,
            'has_history' => true,
            'order_count' => $user->orders->count(),
            'product_history' => $userProductIds,
            'recommendations_count' => count($recommendations),
            'recommendations' => $recommendations,
            'no_duplicates' => $this->verifyNoDuplicates($userProductIds, $recommendations),
            'all_available' => $this->verifyAllAvailable($recommendations),
        ];
    }

    /**
     * Récupère les règles d'association générées par Apriori
     */
    private function getAssociationRules(): array
    {
        try {
            $service = new RecommendationService();
            $apriori = $service->getOrCreateAprioriModel();

            // Récupérer les règles via reflection (php-ml ne les expose pas directement)
            return [
                'status' => 'trained',
                'message' => 'Le modèle Apriori a été entraîné avec succès',
                'note' => 'Les règles d\'association ne sont pas directement accessibles via l\'API php-ml',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Teste la cohérence des recommandations
     */
    private function testCoherence(): array
    {
        $service = new RecommendationService();
        $users = User::limit(5)->get();

        $results = [];
        foreach ($users as $user) {
            $recommendations = $service->suggestForUser($user, 3);
            $results[] = [
                'user_id' => $user->id,
                'recommendations_count' => count($recommendations),
                'valid_structure' => $this->verifyRecommendationStructure($recommendations),
            ];
        }

        return [
            'tested_users' => count($users),
            'results' => $results,
            'all_valid' => collect($results)->every(fn($r) => $r['valid_structure']),
        ];
    }

    /**
     * Vérifie que les recommandations sont des produits populaires
     */
    private function verifyPopularProducts(array $recommendations): bool
    {
        if (empty($recommendations)) {
            return false;
        }

        $popularIds = Product::select('id')
            ->where('is_available', true)
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->limit(10)
            ->pluck('id')
            ->toArray();

        $recommendedIds = array_column($recommendations, 'id');

        // Au moins 50% des recommandations doivent être dans les produits populaires
        $overlap = count(array_intersect($recommendedIds, $popularIds));
        return ($overlap / count($recommendedIds)) >= 0.5;
    }

    /**
     * Vérifie qu'il n'y a pas de doublons avec l'historique
     */
    private function verifyNoDuplicates(array $userHistory, array $recommendations): bool
    {
        if (empty($recommendations)) {
            return true;
        }

        $recommendedIds = array_column($recommendations, 'id');
        $overlap = array_intersect($userHistory, $recommendedIds);

        return empty($overlap);
    }

    /**
     * Vérifie que tous les produits recommandés sont disponibles
     */
    private function verifyAllAvailable(array $recommendations): bool
    {
        if (empty($recommendations)) {
            return true;
        }

        foreach ($recommendations as $product) {
            $productModel = Product::find($product['id']);
            if (!$productModel || !$productModel->is_available || $productModel->stock_quantity <= 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Vérifie la structure des recommandations
     */
    private function verifyRecommendationStructure(array $recommendations): bool
    {
        if (empty($recommendations)) {
            return true;
        }

        $requiredKeys = ['id', 'name', 'price', 'image', 'stock'];

        foreach ($recommendations as $product) {
            foreach ($requiredKeys as $key) {
                if (!array_key_exists($key, $product)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Affiche les résultats de test de manière lisible
     */
    public function displayResults(array $results): void
    {
        echo "\n";
        echo "========================================\n";
        echo "TEST DU SYSTÈME DE RECOMMANDATION\n";
        echo "========================================\n\n";

        // 1. Statistiques des données
        echo "1. STATISTIQUES DES DONNÉES\n";
        echo "----------------------------------------\n";
        foreach ($results['data_stats'] as $key => $value) {
            echo str_pad(ucfirst(str_replace('_', ' ', $key)), 30) . ": {$value}\n";
        }
        echo "\n";

        // 2. Test utilisateur nouveau
        echo "2. TEST UTILISATEUR SANS HISTORIQUE\n";
        echo "----------------------------------------\n";
        $newUserTest = $results['new_user_test'];
        if ($newUserTest['status'] === 'success') {
            echo "Utilisateur: {$newUserTest['user_name']} (ID: {$newUserTest['user_id']})\n";
            echo "Recommandations: {$newUserTest['recommendations_count']}\n";
            echo "Attendu: {$newUserTest['expected']}\n";
            echo "Est populaire: " . ($newUserTest['is_popular'] ? '✅ OUI' : '❌ NON') . "\n";
            echo "\nProduits recommandés:\n";
            foreach ($newUserTest['recommendations'] as $product) {
                echo "  - {$product['name']} ({$product['price']} TND)\n";
            }
        } else {
            echo "Status: {$newUserTest['status']}\n";
            echo "Message: {$newUserTest['message']}\n";
        }
        echo "\n";

        // 3. Test utilisateur existant
        echo "3. TEST UTILISATEUR AVEC HISTORIQUE\n";
        echo "----------------------------------------\n";
        $existingUserTest = $results['existing_user_test'];
        if ($existingUserTest['status'] === 'success') {
            echo "Utilisateur: {$existingUserTest['user_name']} (ID: {$existingUserTest['user_id']})\n";
            echo "Commandes: {$existingUserTest['order_count']}\n";
            echo "Historique produits: " . implode(', ', $existingUserTest['product_history']) . "\n";
            echo "Recommandations: {$existingUserTest['recommendations_count']}\n";
            echo "Pas de doublons: " . ($existingUserTest['no_duplicates'] ? '✅ OUI' : '❌ NON') . "\n";
            echo "Tous disponibles: " . ($existingUserTest['all_available'] ? '✅ OUI' : '❌ NON') . "\n";
            echo "\nProduits recommandés:\n";
            foreach ($existingUserTest['recommendations'] as $product) {
                echo "  - {$product['name']} ({$product['price']} TND, Stock: {$product['stock']})\n";
            }
        } else {
            echo "Status: {$existingUserTest['status']}\n";
            echo "Message: {$existingUserTest['message']}\n";
        }
        echo "\n";

        // 4. Règles d'association
        echo "4. RÈGLES D'ASSOCIATION (APRIORI)\n";
        echo "----------------------------------------\n";
        echo "Status: {$results['association_rules']['status']}\n";
        echo "Message: {$results['association_rules']['message']}\n";
        if (isset($results['association_rules']['note'])) {
            echo "Note: {$results['association_rules']['note']}\n";
        }
        echo "\n";

        // 5. Test de cohérence
        echo "5. TEST DE COHÉRENCE\n";
        echo "----------------------------------------\n";
        echo "Utilisateurs testés: {$results['coherence_test']['tested_users']}\n";
        echo "Tous valides: " . ($results['coherence_test']['all_valid'] ? '✅ OUI' : '❌ NON') . "\n";
        echo "\n";

        echo "========================================\n";
        echo "FIN DES TESTS\n";
        echo "========================================\n\n";
    }
}
