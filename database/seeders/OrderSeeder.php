<?php

namespace Database\Seeders;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // [id, user_id, product_id, quantity, statut, paiement, jours, tracking|null]
        $rows = [
            [1, 7, 1, 6, 'delivered', 'card', 62, 'BDX-2025-00841'],
            [2, 8, 3, 3, 'delivered', 'transfer', 54, 'BDX-2025-00903'],
            [3, 9, 5, 12, 'delivered', 'cash', 47, 'BDX-2025-00955'],
            [4, 10, 2, 4, 'shipped', 'card', 12, 'BDX-2025-01120'],
            [5, 11, 7, 5, 'shipped', 'card', 8, 'BDX-2025-01148'],
            [6, 12, 4, 20, 'pending', 'transfer', 3, null],
            [7, 13, 6, 1, 'pending', 'cash', 2, null],
            [8, 14, 9, 2, 'cancelled', 'card', 16, null],
            [9, 5, 8, 6, 'pending', 'transfer', 1, null],
            [10, 4, 12, 8, 'delivered', 'card', 38, 'BDX-2025-01011'],
            [11, 6, 11, 3, 'shipped', 'cash', 6, 'BDX-2025-01131'],
            [12, 7, 10, 4, 'delivered', 'card', 9, 'BDX-2025-01088'],
        ];

        $insert = [];

        foreach ($rows as [$id, $userId, $productId, $quantity, $status, $payment, $daysAgo, $tracking]) {
            $orderDate = $now->copy()->subDays($daysAgo);
            $product = Product::findOrFail($productId);
            $user = DB::table('users')->where('id', $userId)->first();

            $insert[] = [
                'id' => $id,
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'total_amount' => round((float) $product->price * $quantity, 2),
                'status' => $status,
                'order_date' => $orderDate,
                'shipping_address' => $user->address.', '.$user->city,
                'payment_method' => $payment,
                'tracking_number' => $tracking,
                'created_at' => $orderDate,
                'updated_at' => $orderDate->copy()->addDays(2),
            ];
        }

        DB::table('orders')->insert($insert);
    }
}
