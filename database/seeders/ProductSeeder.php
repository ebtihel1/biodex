<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Database\Seeders\Support\SeedImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // [id, nom, categorie_id, process_id, prix, stock, description, specifications, disponible, jours, image]
        $rows = [
            [1, 'Bouteille PET recyclée 50cl', 1, 1, 12.00, 140, 'Bouteille transparente issu du broyage de bouteilles PET collectées en Tunisie.',
                ['Matière' => 'PET recyclé', 'Contenance' => '50 cl', 'Bouchon' => 'à vis inviolable'], true, 70, ['bouteille-pet-recyclee', '#0ea5e9', '#075985']],
            [2, 'Flacon de savon liquide 250ml', 1, 4, 8.50, 220, 'Flacon en PET recyclé réutilisable, rempli de savon liquide fabriqué à Tunis.',
                ['Matière' => 'PET recyclé', 'Contenance' => '250 ml', 'Usage' => 'ménager'], true, 62, ['flacon-savon', '#7c3aed', '#4c1d95']],
            [3, 'Papier recyclé A4 80g', 3, 7, 6.20, 500, 'Ramette de 500 feuilles issue de journaux et de cartons recyclés.',
                ['Format' => 'A4', 'Grammage' => '80 g/m²', 'Blanc' => 'blanc naturel'], true, 55, ['papier-recycle-a4', '#a16207', '#713f12']],
            [4, 'Carton d’emballage renforcé', 3, 7, 3.40, 800, 'Carton cannelure double pour emballages de produits locaux.',
                ['Type' => 'double cannelure', 'Dimensions' => '40 x 30 x 25 cm'], true, 48, ['carton-emballage', '#ca8a04', '#854d0e']],
            [5, 'Canette en aluminium 33cl', 4, 5, 4.90, 340, 'Canette grand format refondue à partir d’aluminium collecté dans le Grand Tunis.',
                ['Matière' => 'aluminium 99,7 %', 'Contenance' => '33 cl', 'Réutilisable' => 'oui'], true, 41, ['canette-aluminium', '#64748b', '#334155']],
            [6, 'Moule à cuisson en acier recyclé', 4, 9, 29.90, 65, 'Moule de cuisson fabriquée à Sousse à partir d’acier de récupération.',
                ['Matière' => 'acier recyclé', 'Diamètre' => '26 cm', 'Revêtement' => 'antiadhésif'], true, 30, ['moule-acier', '#78716c', '#44403c']],
            [7, 'Bocaux en verre recyclé 1L', 2, 3, 9.90, 180, 'Bocal en verre 100 % recyclé, idéal pour la conservation maison.',
                ['Matière' => 'verre recyclé', 'Contenance' => '1 L', 'Bouchon' => 'couvercle inclus'], true, 33, ['bocaux-verre', '#16a34a', '#14532d']],
            [8, 'Panneau de particules de bois', 8, 11, 45.00, 40, 'Panneau fabriqué à partir de palettes de récupération broyées.',
                ['Matière' => 'bois recyclé', 'Épaisseur' => '18 mm', 'Format' => '2500 x 1250 mm'], false, 5, ['panneau-bois', '#b45309', '#78350f']],
            [9, 'Pull en laine recyclée', 7, 9, 34.50, 55, 'Pull en maille épaisse issu de fibres textiles récupérées et retriées.',
                ['Matière' => 'laine recyclée', 'Taille' => 'M', 'Entretien' => 'lavage à 30 °C'], true, 24, ['pull-laine', '#db2777', '#831843']],
            [10, 'Sac en toile de coton recyclé', 7, 9, 15.90, 120, 'Sac de courses en toile épaisse issu de shirts réemployés.',
                ['Matière' => 'coton recyclé', 'Capacité' => '15 L', 'Poignées' => 'longues'], true, 18, ['sac-toile', '#9333ea', '#581c87']],
            [11, 'Composteur de cuisine 20L', 5, 6, 59.00, 28, 'Composteur de table en plastique recyclé pour les déchets organiques du foyer.',
                ['Volume' => '20 L', 'Matière' => 'plastique recyclé', 'Filtres' => 'charbon actif inclus'], true, 12, ['composteur-20l', '#65a30d', '#365314']],
            [12, 'Sac de collecte tissues 30L', 1, 4, 7.40, 260, 'Rouleau de sacs en tissu compostable pour le tri à la source.',
                ['Capacité' => '30 L', 'Quantité' => '20 sacs', 'Compostable' => 'oui'], true, 9, ['sacs-tissus', '#0891b2', '#164e63']],
        ];

        $insert = [];

        foreach ($rows as [$id, $name, $categoryId, $processId, $price, $stock, $description, $specifications, $available, $daysAgo, $image]) {
            $createdAt = $now->copy()->subDays($daysAgo);

            $insert[] = [
                'id' => $id,
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'stock_quantity' => $stock,
                'waste_category_id' => $categoryId,
                'recycling_process_id' => $processId,
                'image_path' => SeedImage::make('products', $image[0], $name, [$image[1], $image[2]]),
                'specifications' => json_encode($specifications, JSON_UNESCAPED_UNICODE),
                'is_available' => $available,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(2),
            ];
        }

        DB::table('products')->insert($insert);
    }
}
