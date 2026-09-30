<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Database\Seeders\Support\SeedImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // [id, user_id, titre, description, statut, début(-j), fin(+j), ville, région, inscrits]
        $rows = [
            [1, 3, 'Grand Ramadan, Grand Tri', 'Trente jours de collecte dans les quartiers de Tunis, Sousse et Bizerte. Chaque tonne collectée est triée et réinjectée dans la filière du verre et du papier.', 'active', 12, 18, 'Tunis', 'Grand Tunis', 148],
            [2, 5, 'Une seconde vie pour le textile', 'Collecte de vêtements, chaussures et accessoires dans les magasins partenaires, avec tri et orientation vers la friperie et le recyclage du tissu.', 'active', 25, 40, 'Sousse', 'Sahel', 96],
            [3, 4, 'Zéro déchet au bureau', 'Programme de prévention pour les entreprises : audit, réduction des emballages et installation de bacs de tri dans les espaces de travail.', 'active', 5, 55, 'Ariana', 'Grand Tunis', 61],
            [4, 3, 'Collecte spéciale déchets électroniques', 'Reprise gratuite des ordinateurs, téléphones et petits électroménagers avec effacement sécurisé des données.', 'active', 2, 28, 'Bizerte', 'Nord-Est', 74],
            [5, 6, 'Jardin partagé et compostage', 'Accompagnement des associations pour créer des jardins partagés et des unités de compostage de proximité.', 'closed', -75, -10, 'Nabeul', 'Cap Bon', 132],
            [6, 5, 'Trie et tri du plastique', 'Campagne de sensibilisation sur le tri du plastique en zone côtière, en lien avec les associations de protection du littoral.', 'draft', -3, 45, 'Mahdia', 'Sud-Est', 0],
        ];

        $insert = [];

        foreach ($rows as [$id, $userId, $title, $description, $status, $startInDays, $endInDays, $city, $region, $participants]) {
            $startDate = $now->copy()->addDays($startInDays);
            $createdAt = $startDate->copy()->subDays(20);

            $insert[] = [
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'image' => SeedImage::url('campaigns', 'campagne-'.$id, $title, ['#059669', '#065f46']),
                'start_date' => $startDate->toDateString(),
                'end_date' => $startDate->copy()->addDays(abs($endInDays))->toDateString(),
                'status' => $status,
                'user_id' => $userId,
                'deadline_registration' => $startDate->copy()->addDays(max(1, (int) floor(abs($endInDays) / 2)))->toDateString(),
                'city' => $city,
                'region' => $region,
                'participants_count' => $participants,
                'created_at' => $createdAt,
                'updated_at' => $now->copy()->subDays(1),
            ];
        }

        DB::table('campaigns')->insert($insert);
    }
}
