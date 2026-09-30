<?php

namespace Database\Seeders;

use App\Models\RepairService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // [id, user_id, repair_service_id, statut, cout estimé, dans (jours), description, notes, créé (jours)]
        $rows = [
            [1, 7, 1, 'completed', 60.00, -24, 'Écran fissuré sur un téléphone Nokia 8', null, 30],
            [2, 8, 2, 'completed', 90.00, -18, 'Clavier unresponsive sur un portable Lenovo', null, 25],
            [3, 9, 3, 'completed', 120.00, -12, 'Fuite d’eau au niveau du hublot', 'Pièce d’étanchéité remplacée.', 20],
            [4, 10, 4, 'confirmed', 110.00, 0, 'Réfrigérateur ne refroidit plus la partie basse', null, 6],
            [5, 11, 10, 'confirmed', 80.00, 3, 'Split mural à nettoyer et recharger', 'Intervention en matinée.', 4],
            [6, 12, 8, 'confirmed', 45.00, 2, 'Freins arrière lâches sur un vélo de ville', null, 3],
            [7, 13, 5, 'pending', 130.00, 6, 'Machine à laver qui saute à l’essorage', null, 1],
            [8, 14, 7, 'pending', 35.00, 9, 'Pantalon à rallonger et zipper cassé', null, 1],
            [9, 5, 6, 'confirmed', 140.00, 11, 'Fauteuil en bois dont l’assise est affaissée', 'Bois fourni par le client.', 5],
            [10, 4, 9, 'pending', 250.00, 14, 'Installation de panneaux solaires sur une villa', 'Toiture sud-est, 120 m².', 2],
        ];

        $insert = [];

        foreach ($rows as [$id, $userId, $serviceId, $status, $cost, $inDays, $item, $notes, $createdDaysAgo]) {
            $createdAt = $now->copy()->subDays($createdDaysAgo);
            $service = RepairService::findOrFail($serviceId);

            $insert[] = [
                'id' => $id,
                'user_id' => $userId,
                'repair_service_id' => $serviceId,
                'booking_date' => $now->copy()->addDays($inDays)->setTime(10, 0),
                'status' => $status,
                'notes' => $notes,
                'item_description' => $item,
                'estimated_cost' => $cost ?? $service->price,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDay(),
            ];
        }

        DB::table('bookings')->insert($insert);
    }
}
