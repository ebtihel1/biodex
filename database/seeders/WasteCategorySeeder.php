<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WasteCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $rows = [
            [
                'id' => 1,
                'name' => 'Plastique',
                'description' => 'Bouteilles, flacons, emballages souples et rigides en PET ou HDPE.',
                'recycling_instructions' => 'Vider et rincer le contenant, retirer le bouchon puis déposer le flacon dans le bac bleu. Les plastiques souples (sachets, films) sont refusés.',
                'created_at' => $now->copy()->subDays(240),
                'updated_at' => $now->copy()->subDays(12),
            ],
            [
                'id' => 2,
                'name' => 'Verre',
                'description' => 'Bouteilles, bocaux et pots en verre incolore, vert ou ambré.',
                'recycling_instructions' => 'Enlever les bouchons et les étiquettes, ne pas casser le verre et le livrer entier au point de collecte pour éviter toute blessure.',
                'created_at' => $now->copy()->subDays(238),
                'updated_at' => $now->copy()->subDays(40),
            ],
            [
                'id' => 3,
                'name' => 'Papier-Carton',
                'description' => 'Journaux, magazines, enveloppes, cartons d’emballage et cartonnettes.',
                'recycling_instructions' => 'Aplatir les cartons, retirer les films plastiques et les agrafes, puis déposer le tout à plat pour optimiser le transport.',
                'created_at' => $now->copy()->subDays(236),
                'updated_at' => $now->copy()->subDays(31),
            ],
            [
                'id' => 4,
                'name' => 'Métal',
                'description' => 'Boîtes de conserve, canettes en aluminium, fûts et pièces métalliques.',
                'recycling_instructions' => 'Rincer les contenants, ne pas aplatir les canettes et les déposer séparément du plastique dans le bac dédié.',
                'created_at' => $now->copy()->subDays(234),
                'updated_at' => $now->copy()->subDays(19),
            ],
            [
                'id' => 5,
                'name' => 'Déchet organique',
                'description' => 'Restes alimentaires, épluchures, feuilles et déchets de jardin.',
                'recycling_instructions' => 'Composter à domicile si possible, sinon utiliser un composteur de proximité. Aucun sac plastique accepté.',
                'created_at' => $now->copy()->subDays(230),
                'updated_at' => $now->copy()->subDays(8),
            ],
            [
                'id' => 6,
                'name' => 'Déchets électroniques',
                'description' => 'Téléphones, ordinateurs, chargeurs, périphériques et petits électroménagers.',
                'recycling_instructions' => 'Apporter les appareils au point de collecte agréé. Les données personnelles doivent être effacées avant le dépôt.',
                'created_at' => $now->copy()->subDays(225),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'id' => 7,
                'name' => 'Textile',
                'description' => 'Vêtements, chaussures, draps, rideaux et accessoires.',
                'recycling_instructions' => 'Déposer les vêtements propres et secs dans un sac fermé pour la friperie ou le recyclage du tissu.',
                'created_at' => $now->copy()->subDays(220),
                'updated_at' => $now->copy()->subDays(3),
            ],
            [
                'id' => 8,
                'name' => 'Bois',
                'description' => 'Meubles, palettes, cagettes et chutes de menuiserie non traités.',
                'recycling_instructions' => 'Les bois contaminés par des produits chimiques sont refusés. Démonter les meubles pour faciliter le broyage.',
                'created_at' => $now->copy()->subDays(215),
                'updated_at' => $now->copy()->subDays(2),
            ],
        ];

        DB::table('waste_categories')->insert($rows);
    }
}
