<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Database\Seeders\Support\SeedImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WasteSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // [id, user_id, collection_point_id, category_id, type, weight, status, description, daysAgo, image]
        $rows = [
            [1, 7, 1, 1, 'Bouteilles PET 1.5L', 18.40, 'recyclable', 'Caisses de bouteilles d’eau minérale accumulées pendant une semaine.', 88, ['bouteilles-pet', '#0ea5e9', '#0369a1']],
            [2, 8, 1, 3, 'Journaux et magazines', 24.75, 'recyclable', 'Un sac complet de papier journal imprimé, encore en bon état.', 81, ['journaux-magazines', '#a16207', '#713f12']],
            [3, 9, 2, 2, 'Bouteilles en verre', 32.10, 'recyclable', 'Bouteilles de jus et de limonade, rincees et triees par couleur.', 74, ['bouteilles-verre', '#16a34a', '#166534']],
            [4, 10, 3, 1, 'Flacons de shampooing', 6.25, 'recyclable', 'Flacons vides rincés, bouchon retiré conformément aux consignes.', 66, ['flacons-shampooing', '#7c3aed', '#4c1d95']],
            [5, 11, 4, 4, 'Canettes en aluminium', 9.80, 'recyclable', 'Canettes de soda et de bière accumulées au bureau.', 59, ['canettes-aluminium', '#64748b', '#334155']],
            [6, 12, 5, 5, 'Restes de jardin', 14.60, 'recyclable', 'Feuilles mortes et petites branches issues du jardin.', 52, ['restes-jardin', '#65a30d', '#365314']],
            [7, 13, 2, 3, 'Cartons d’emballage', 27.30, 'recyclable', 'Cartons d’emballage d’électroménagers aplatis et ficelés.', 45, ['cartons-emballage', '#ca8a04', '#854d0e']],
            [8, 14, 3, 6, 'Téléphone et chargeurs', 0.85, 'reusable', 'Ancien smartphone et câbles, données effacées avant dépôt.', 38, ['telephone-chargeurs', '#dc2626', '#7f1d1d']],
            [9, 7, 1, 2, 'Pots et bocaux', 21.00, 'recyclable', 'Pots de confiture et bocaux de fruits en verre.', 31, ['pots-bocaux', '#059669', '#064e3b']],
            [10, 8, 4, 4, 'Ferraille de cuisine', 16.45, 'recyclable', 'Casseroles, poêles et pièces de serrurerie usagées.', 26, ['ferraille-cuisine', '#57534e', '#292524']],
            [11, 9, 2, 7, 'Vêtements en textile', 11.20, 'reusable', 'Vêtements propres et secs, prêts pour la friperie.', 20, ['vetements-textile', '#db2777', '#831843']],
            [12, 10, 3, 6, 'Ordinateur portable', 4.70, 'reusable', 'Ordinateur portable hors service, écran fissuré.', 16, ['ordinateur-portable', '#4f46e5', '#312e81']],
            [13, 11, 5, 1, 'Sacs et films plastiques', 3.90, 'recyclable', 'Films d’emballage et sachets collectés dans un conteneur dédié.', 11, ['films-plastiques', '#0891b2', '#164e63']],
            [14, 12, 4, 8, 'Palettes en bois', 48.00, 'recyclable', 'Palettes de récupération en bois non traité.', 7, ['palettes-bois', '#b45309', '#78350f']],
            [15, 13, 1, 3, 'Cartons ondulés et enveloppes', 15.65, 'recyclable', 'Mélange de carton ondulé et de papier administratif.', 4, ['cartons-ondules', '#a3a3a3', '#525252']],
            [16, 14, 3, 7, 'Chaussures et sacs', 7.35, 'reusable', 'Paires de chaussures et sacs à main en bon état.', 2, ['chaussures-sacs', '#9333ea', '#581c87']],
        ];

        $insert = [];

        foreach ($rows as [$id, $userId, $pointId, $categoryId, $type, $weight, $status, $description, $daysAgo, $image]) {
            $createdAt = $now->copy()->subDays($daysAgo);

            $insert[] = [
                'id' => $id,
                'user_id' => $userId,
                'collection_point_id' => $pointId,
                'waste_category_id' => $categoryId,
                'type' => $type,
                'weight' => $weight,
                'status' => $status,
                'description' => $description,
                'image_path' => SeedImage::make('wastes', $image[0], $type, [$image[1], $image[2]]),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(2),
            ];
        }

        DB::table('wastes')->insert($insert);
    }
}
