<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CollectionPointSeeder extends Seeder
{
    public function run()
    {
        DB::table('collection_points')->insert([
            [
                'name' => 'Centre de Collecte Tunis Nord',
                'address' => 'Rue de l’Environnement, Cité El Khadra',
                'city' => 'Tunis',
                'postal_code' => '1003',
                'latitude' => 36.8429,
                'longitude' => 10.1934,
                'opening_hours' => json_encode([
                    'lundi-vendredi' => '08:00 - 17:00',
                    'samedi' => '08:00 - 13:00',
                    'dimanche' => 'Fermé',
                ]),
                'accepted_categories' => json_encode([1, 2, 3]),
                'contact_phone' => '+216 71 234 567',
                'status' => 'active', // string
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Centre de Collecte Sousse',
                'address' => 'Avenue de l’Écologie, Sousse',
                'city' => 'Sousse',
                'postal_code' => '4000',
                'latitude' => 35.8256,
                'longitude' => 10.6360,
                'opening_hours' => json_encode([
                    'lundi-vendredi' => '08:00 - 16:30',
                    'samedi' => '08:00 - 12:30',
                    'dimanche' => 'Fermé',
                ]),
                'accepted_categories' => json_encode([1, 3]),
                'contact_phone' => '+216 73 456 789',
                'status' => 'inactive', // string
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
