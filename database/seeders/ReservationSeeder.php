<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // [id, user_id, product_id, quantity, statut, reservee depuis (jours), expire dans (jours)]
        $rows = [
            [1, 7, 1, 10, 'confirmed', 5, 2],
            [2, 8, 3, 2, 'pending', 3, 4],
            [3, 9, 5, 24, 'confirmed', 9, 1],
            [4, 10, 2, 6, 'pending', 1, 6],
            [5, 11, 7, 8, 'confirmed', 12, -1],
            [6, 12, 4, 30, 'cancelled', 18, 0],
            [7, 13, 6, 1, 'pending', 2, 5],
            [8, 14, 10, 3, 'confirmed', 7, 3],
            [9, 5, 12, 15, 'pending', 1, 7],
            [10, 4, 9, 2, 'confirmed', 4, 2],
        ];

        $insert = [];

        foreach ($rows as [$id, $userId, $productId, $quantity, $status, $createdDaysAgo, $expiresInDays]) {
            $createdAt = $now->copy()->subDays($createdDaysAgo);

            $insert[] = [
                'id' => $id,
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'reserved_until' => $now->copy()->addDays($expiresInDays)->setTime(18, 0),
                'status' => $status,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addHours(6),
            ];
        }

        DB::table('reservations')->insert($insert);
    }
}
