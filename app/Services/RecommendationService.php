<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Phpml\Association\Apriori;

class RecommendationService
{
    /**
     * Suggère des produits pour un utilisateur basé sur l'algorithme Apriori
     *
     * @param User $user
     * @param int $limit Nombre de recommandations à retourner
     * @return array
     */
    public function suggestForUser(User $user, int $limit = 5): array
    {
        // 1. Récupérer l'historique des produits de l'utilisateur
        $userProductIds = $this->getUserProductHistory($user);

        // 2. Si l'utilisateur n'a pas d'historique, retourner les produits populaires
        if (empty($userProductIds)) {
            return $this->getPopularProducts($limit);
        }

        // 3. Récupérer ou entraîner le modèle Apriori (avec cache)
        $apriori = $this->getOrCreateAprioriModel();

        // 4. Faire des prédictions basées sur l'historique de l'utilisateur
        $recommendations = $apriori->predict($userProductIds);

        // 5. Filtrer les produits déjà achetés et récupérer les détails
        $suggestions = [];
        foreach ($recommendations as $productId) {
            // Ignorer les produits déjà achetés
            if (in_array($productId, $userProductIds)) {
                continue;
            }

            // Récupérer les détails du produit
            $product = Product::find($productId);
            if ($product && $product->is_available) {
                $suggestions[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image_path,
                    'stock' => $product->stock_quantity,
                ];

                if (count($suggestions) >= $limit) {
                    break;
                }
            }
        }

        // 6. Fallback: si pas assez de recommandations, compléter avec les produits populaires
        if (count($suggestions) < $limit) {
            $popularProducts = $this->getPopularProducts($limit - count($suggestions));
            $suggestions = array_merge($suggestions, $popularProducts);
        }

        return $suggestions;
    }

    /**
     * Récupère l'historique des produits d'un utilisateur (commandes + réservations)
     */
    private function getUserProductHistory(User $user): array
    {
        // Récupérer les produits des commandes
        $orderProductIds = Order::where('user_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->pluck('product_id')
            ->toArray();

        // Récupérer les produits des réservations
        $reservationProductIds = $user->reservations()
            ->where('status', '!=', 'cancelled')
            ->pluck('product_id')
            ->toArray();

        // Fusionner et dédoublonner
        return array_unique(array_merge($orderProductIds, $reservationProductIds));
    }

    /**
     * Construit les transactions pour l'entraînement Apriori
     * Chaque transaction = les produits achetés dans une même commande
     */
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
                    $products[] = $order->product_id;
                }
            }

            // Ajouter la transaction si elle contient des produits
            if (!empty($products)) {
                $transactions[] = array_unique($products);
            }
        }

        return $transactions;
    }

    /**
     * Récupère ou crée le modèle Apriori (avec cache)
     */
    private function getOrCreateAprioriModel(): Apriori
    {
        $cacheKey = 'apriori_model_v2';
        $cacheDuration = 3600; // 1 heure

        return Cache::remember($cacheKey, $cacheDuration, function () {
            // Construire les transactions à partir des commandes réelles
            $transactions = $this->buildTransactions();

            // Récupérer tous les IDs de produits disponibles
            $allProductIds = Product::where('is_available', true)
                ->pluck('id')
                ->toArray();

            // Si pas assez de données, utiliser des seuils plus bas
            $transactionCount = count($transactions);
            $support = $transactionCount > 50 ? 0.1 : 0.05; // Support: 10% ou 5%
            $confidence = 0.6; // Confidence: 60%

            // Créer et entraîner le modèle
            $apriori = new Apriori($support, $confidence);
            $apriori->train($transactions, $allProductIds);

            \Log::info('Apriori model trained', [
                'transactions' => $transactionCount,
                'products' => count($allProductIds),
                'support' => $support,
                'confidence' => $confidence,
            ]);

            return $apriori;
        });
    }

    /**
     * Récupère les produits les plus populaires (fallback)
     */
    private function getPopularProducts(int $limit = 5): array
    {
        $cacheKey = 'popular_products';
        $cacheDuration = 1800; // 30 minutes

        return Cache::remember($cacheKey, $cacheDuration, function () use ($limit) {
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

    /**
     * Invalide le cache du modèle Apriori (à appeler après une nouvelle commande)
     */
    public function invalidateCache(): void
    {
        Cache::forget('apriori_model_v2');
        Cache::forget('popular_products');
    }
}