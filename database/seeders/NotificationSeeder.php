<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // [id, user_id, titre, message, type, lue, type_cible|null, id_cible|null, url|null, jours]
        $rows = [
            [1, 7, 'Votre commande est livrée', 'La commande BDX-2025-00841 a été livrée. Merci de confirmer la réception.', 'success', true, 'Order', 1, '/biodex/orders/1', 60],
            [2, 7, 'Nouvelle donation disponible', 'Un smartphone reconditionné a été ajouté au catalogue des dons.', 'info', false, 'Donation', 1, '/biodex/donations/1', 34],
            [3, 7, 'Campagne ouverte à Tunis', 'La campagne Grand Ramadan, Grand Tri a démarré dans votre ville.', 'info', false, 'Campaign', 1, '/campaignsFront', 12],
            [4, 8, 'Votre inscription est confirmée', 'Votre participation à la campagne Grand Ramadan, Grand Tri a été validée.', 'success', true, 'Campaign', 1, '/campaignsFront', 38],
            [5, 8, 'Collecte de textile en Sousse', 'Le point de collecte Sousse accepte désormais les textile et le carton.', 'info', false, 'CollectionPoint', 2, '/biodex/collectionpoints/2', 20],
            [6, 9, 'Objectif de poids atteint', 'Vous avez déposé 32 kg de verre, bravo pour votre engagement.', 'success', false, 'Waste', 3, '/wastess/3', 72],
            [7, 10, 'Ordinateur portable réservé', 'Votre réservation du portable est confirmée, à récupérer au point vert des Berges du Lac.', 'warning', false, 'Reservation', 3, '/biodex/reservations/3', 9],
            [8, 10, 'Donation en attente', 'Votre donation d’ordinateur portable pour études a été réservée par un particulier.', 'info', false, 'Donation', 3, '/biodex/donations/3', 21],
            [9, 11, 'Réparation terminée', 'Votre appareil a été réparé et vous pouvez venir le récupérer.', 'success', true, 'Booking', 5, null, 4],
            [10, 12, 'Paiement en attente', 'Votre commande de cartons d’emballage attend toujours la validation du virement.', 'warning', false, 'Order', 6, '/biodex/orders/6', 3],
            [11, 13, 'Rappel de tri', 'Les films plastiques doivent être déposés séparément du plastique rigide.', 'info', false, 'WasteCategory', 1, '/categories/1', 11],
            [12, 14, 'Nouvelle participation', 'Vous vous êtes inscrit à la campagne de collecte des déchets électroniques.', 'info', false, 'Campaign', 4, '/campaignsFront', 2],
            [13, 1, 'Nouveau déchet signalé', 'Un dépôt de 15,6 kg de carton a été enregistré au point Tunis Nord.', 'info', false, 'Waste', 15, '/wastes/15', 4],
            [14, 1, 'Processus de recyclage terminé', 'Le broyage des bouteilles PET est terminé, 14,2 kg de flakes sont disponibles.', 'success', false, 'RecyclingProcess', 1, '/recyclingprocesses/1', 80],
            [15, 2, 'Stock faible', 'Le stock de flacons de savon est passé sous le seuil d’alerte.', 'warning', true, 'Product', 2, '/products/2', 2],
        ];

        $insert = [];

        foreach ($rows as [$id, $userId, $title, $message, $type, $isRead, $entityType, $entityId, $url, $daysAgo]) {
            $createdAt = $now->copy()->subDays($daysAgo);

            $insert[] = [
                'id' => $id,
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => $isRead,
                'related_entity_type' => $entityType,
                'related_entity_id' => $entityId,
                'action_url' => $url,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        DB::table('notifications')->insert($insert);
    }
}
