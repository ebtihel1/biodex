<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollectionPointSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $points = [
            [
                'id' => 1,
                'name' => 'Centre de Collecte Tunis Nord',
                'address' => 'Rue de l’Environnement, Cité El Khadra',
                'city' => 'Tunis',
                'postal_code' => '1003',
                'latitude' => 36.84290,
                'longitude' => 10.19340,
                'contact_phone' => '+216 71 234 567',
                'status' => 'active',
                'opening_hours' => [
                    'lundi-vendredi' => '08:00 - 17:00',
                    'samedi' => '08:00 - 13:00',
                    'dimanche' => 'Fermé',
                ],
                'accepted_categories' => [1, 2, 3, 4, 7],
                'created_at' => $now->copy()->subDays(230),
                'updated_at' => $now->copy()->subDays(4),
            ],
            [
                'id' => 2,
                'name' => 'Centre de Collecte Sousse',
                'address' => 'Avenue de l’Écologie, route de Monastir km 2',
                'city' => 'Sousse',
                'postal_code' => '4000',
                'latitude' => 35.82560,
                'longitude' => 10.63600,
                'contact_phone' => '+216 73 456 789',
                'status' => 'active',
                'opening_hours' => [
                    'lundi-vendredi' => '08:00 - 16:30',
                    'samedi' => '08:00 - 12:30',
                    'dimanche' => 'Fermé',
                ],
                'accepted_categories' => [1, 3, 4, 5],
                'created_at' => $now->copy()->subDays(205),
                'updated_at' => $now->copy()->subDays(11),
            ],
            [
                'id' => 3,
                'name' => 'Point Vert Les Berges du Lac',
                'address' => 'Centre commercial Les Berges du Lac 2, niveau 0',
                'city' => 'Tunis',
                'postal_code' => '1052',
                'latitude' => 36.85270,
                'longitude' => 10.22780,
                'contact_phone' => '+216 71 963 214',
                'status' => 'active',
                'opening_hours' => [
                    'lundi-samedi' => '09:00 - 19:00',
                    'dimanche' => '09:00 - 13:00',
                ],
                'accepted_categories' => [1, 2, 3, 6, 7],
                'created_at' => $now->copy()->subDays(180),
                'updated_at' => $now->copy()->subDays(2),
            ],
            [
                'id' => 4,
                'name' => 'Déchetterie Charguia',
                'address' => 'Zone industrielle Charguia II, entrée nord',
                'city' => 'Tunis',
                'postal_code' => '2035',
                'latitude' => 36.77200,
                'longitude' => 10.22800,
                'contact_phone' => '+216 71 392 480',
                'status' => 'active',
                'opening_hours' => [
                    'lundi-vendredi' => '07:30 - 16:00',
                    'samedi' => '07:30 - 12:00',
                    'dimanche' => 'Fermé',
                ],
                'accepted_categories' => [1, 2, 3, 4, 5, 6, 7, 8],
                'created_at' => $now->copy()->subDays(165),
                'updated_at' => $now->copy()->subDays(6),
            ],
            [
                'id' => 5,
                'name' => 'Point de Collecte Nabeul',
                'address' => 'Place de l’Indépendance, face à la poste',
                'city' => 'Nabeul',
                'postal_code' => '8000',
                'latitude' => 36.45620,
                'longitude' => 10.73770,
                'contact_phone' => '+216 72 231 905',
                'status' => 'active',
                'opening_hours' => [
                    'lundi-vendredi' => '08:00 - 15:00',
                    'samedi' => '08:00 - 12:00',
                    'dimanche' => 'Fermé',
                ],
                'accepted_categories' => [1, 2, 3, 5, 7],
                'created_at' => $now->copy()->subDays(140),
                'updated_at' => $now->copy()->subDays(9),
            ],
            [
                'id' => 6,
                'name' => 'Centre de Tri Sfax',
                'address' => 'Route de Gabès km 3, zone artisanale',
                'city' => 'Sfax',
                'postal_code' => '3000',
                'latitude' => 34.74050,
                'longitude' => 10.76030,
                'contact_phone' => '+216 74 402 118',
                'status' => 'inactive',
                'opening_hours' => [
                    'lundi-vendredi' => '08:00 - 15:30',
                    'samedi' => 'Fermé',
                    'dimanche' => 'Fermé',
                ],
                'accepted_categories' => [3, 4, 8],
                'created_at' => $now->copy()->subDays(120),
                'updated_at' => $now->copy()->subDays(20),
            ],
        ];

        $insert = [];

        foreach ($points as $point) {
            $point['opening_hours'] = json_encode($point['opening_hours'], JSON_UNESCAPED_UNICODE);
            $point['accepted_categories'] = json_encode($point['accepted_categories']);
            $insert[] = $point;
        }

        DB::table('collection_points')->insert($insert);
    }
}
