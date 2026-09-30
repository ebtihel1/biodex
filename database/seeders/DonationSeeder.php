<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Database\Seeders\Support\SeedImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // [id, user_id, waste_id, item_name, condition, status, description, pickup, adresse, jours]
        $rows = [
            [1, 7, 8, 'Smartphone reconditionné', 'used', 'available', 'Téléphone remis à neuf, écran d’origine changé et batterie remplacée. Fonctionne parfaitement, déverrouillage de 3 jours.', false, null, 34],
            [2, 9, 11, 'Lot de vêtements pour enfants', 'used', 'available', 'Une vingtaine de vêtements pour enfants de 2 à 6 ans, tous propres et repassés.', true, 'Avenue Taieb Mhiri 45, Sousse', 27],
            [3, 10, 12, 'Ordinateur portable pour études', 'used', 'claimed', 'Portable 15 pouces, 8 Go de RAM, testé avec sac et chargeur. Idéal pour les étudiants.', true, 'Résidence Al Qods, appartement 12, Tunis', 21],
            [4, 14, 16, 'Paires de chaussures et sacs à main', 'used', 'available', 'Trois paires de chaussures en bon état et deux sacs à main, donnés volontairement.', true, 'Rue du Port 21, Mahdia', 14],
            [5, 7, 9, 'Bocaux et pots en verre', 'new', 'completed', 'Lot de dix bocaux stérilisés, parfaits pour le maraîchage et les conserves maison.', false, null, 11],
            [6, 8, 4, 'Flacons de produit d’entretien vides', 'used', 'available', 'Flacons bien rinçés, parfaits pour fabriquer du savon ou du nettoyant à la maison.', true, 'Cité El Khadra, rue des Orangers, Ariana', 8],
            [7, 11, 13, 'Sacs et films plastiques propres', 'used', 'available', 'Sacs de courses lavés et triés, prêts pour le réemploi ou le compostage.', false, null, 5],
            [8, 12, 14, 'Palettes pour le jardin', 'used', 'available', 'Huit palettes en bois sec, non traitées, utiles pour créer des jardinières ou des meubles.', true, 'Cité Menzel Bourguiba, impasse 3, Bizerte', 3],
        ];

        $insert = [];

        foreach ($rows as [$id, $userId, $wasteId, $itemName, $condition, $status, $description, $pickup, $address, $daysAgo]) {
            $createdAt = $now->copy()->subDays($daysAgo);

            $images = json_encode([
                SeedImage::make('donations', 'don-'.$id.'-a', $itemName, ['#16a34a', '#0f766e']),
                SeedImage::make('donations', 'don-'.$id.'-b', $itemName.' (vue 2)', ['#0f766e', '#134e4a']),
            ], JSON_UNESCAPED_UNICODE);

            $insert[] = [
                'id' => $id,
                'user_id' => $userId,
                'waste_id' => $wasteId,
                'item_name' => $itemName,
                'condition' => $condition,
                'status' => $status,
                'description' => $description,
                'images' => $images,
                'pickup_required' => $pickup,
                'pickup_address' => $address,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDay(),
            ];
        }

        DB::table('donations')->insert($insert);
    }
}
