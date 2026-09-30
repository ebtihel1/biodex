<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParticipationSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // [id, campaign_id, user_id, statut, inscription (jours avant aujourd'hui)]
        $rows = [
            [1, 1, 7, 'approved', 40],
            [2, 1, 8, 'approved', 38],
            [3, 1, 9, 'approved', 33],
            [4, 1, 10, 'approved', 29],
            [5, 1, 12, 'approved', 22],
            [6, 1, 13, 'approved', 15],
            [7, 1, 14, 'pending', 4],
            [8, 2, 7, 'approved', 30],
            [9, 2, 9, 'approved', 26],
            [10, 2, 11, 'approved', 19],
            [11, 2, 13, 'rejected', 12],
            [12, 2, 8, 'approved', 7],
            [13, 3, 4, 'approved', 20],
            [14, 3, 5, 'approved', 18],
            [15, 3, 6, 'approved', 9],
            [16, 3, 14, 'pending', 2],
            [17, 4, 8, 'approved', 16],
            [18, 4, 10, 'approved', 11],
            [19, 4, 12, 'approved', 6],
            [20, 4, 13, 'pending', 1],
            [21, 5, 7, 'approved', 80],
            [22, 5, 10, 'approved', 78],
            [23, 5, 12, 'approved', 70],
            [24, 5, 14, 'approved', 60],
            [25, 6, 14, 'pending', 1],
        ];

        $insert = [];

        foreach ($rows as [$id, $campaignId, $userId, $status, $daysAgo]) {
            $createdAt = $now->copy()->subDays($daysAgo);

            $insert[] = [
                'id' => $id,
                'campaign_id' => $campaignId,
                'user_id' => $userId,
                'status' => $status,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDay(),
            ];
        }

        DB::table('participations')->insert($insert);
    }
}
