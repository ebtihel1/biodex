<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RepairServiceSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // [id, service, reparateur_id, categorie, prix, duree, description, disponible, jours]
        $rows = [
            [1, 'Réparation de smartphone', 3, 'Mobile & informatique', 60.00, '3 à 5 jours', 'Remplacement d’écran, de batterie ou de connecteur de charge sur les marques les plus courantes.', true, 150],
            [2, 'Nettoyage et réparation d’ordinateur portable', 3, 'Mobile & informatique', 90.00, '5 à 7 jours', 'Remplacement clavier, SSD, ventilation et vérification complète de la carte mère.', true, 148],
            [3, 'Réparation de lave-linge', 4, 'Électroménager', 120.00, '2 à 4 jours', 'Intervention à domicile pour les fuites d’eau, la pompe, le tambour ou la carte de commande.', true, 140],
            [4, 'Dépannage de réfrigérateur', 4, 'Électroménager', 110.00, '1 à 3 jours', 'Recharge de gaz, remplacement du thermostat ou du compresseur.', true, 132],
            [5, 'Réfection de machine à laver', 6, 'Électroménager', 130.00, '2 à 5 jours', 'Remplacement du tambour, des amortisseurs et de la carte de puissance.', true, 120],
            [6, 'Reprise de fauteuil en bois', 5, 'Mobilier', 140.00, '10 à 15 jours', 'Restauration, consolidation de la structure et refabrication d’assise à partir de bois recyclé.', true, 110],
            [7, 'Reprise textile et couture', 5, 'Textile', 35.00, '5 à 7 jours', 'Recouture, changement de fermeture et remise en forme de vêtements.', true, 95],
            [8, 'Réparation de vélo et tandems', 10, 'Cyclerie', 45.00, '2 à 3 jours', 'Remplacement des freins, du dérailleur, des pneus et réglage de la transmission.', true, 80],
            [9, 'Vente et installation de panneaux solaires', 6, 'Énergie', 250.00, '15 à 30 jours', 'Étude de dimensionnement, pose de l’onduleur et suivi de production.', false, 60],
            [10, 'Maintenance de climatiseur', 4, 'Électroménager', 80.00, '1 à 2 jours', 'Nettoyage, désinfection et recharge de gaz des split et VRF.', true, 45],
        ];

        $insert = [];

        foreach ($rows as [$id, $service, $userId, $category, $price, $duration, $description, $available, $daysAgo]) {
            $createdAt = $now->copy()->subDays($daysAgo);

            $insert[] = [
                'id' => $id,
                'service_name' => $service,
                'description' => $description,
                'price' => $price,
                'duration' => $duration,
                'category' => $category,
                'is_available' => $available,
                'user_id' => $userId,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(3),
            ];
        }

        DB::table('repair_services')->insert($insert);
    }
}
