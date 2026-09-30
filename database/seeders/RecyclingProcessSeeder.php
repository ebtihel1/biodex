<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecyclingProcessSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // [id, waste_id, responsable_id, methode, description, start(days ago), end(+days|null), status, output_qty, qualite, notes]
        $rows = [
            [1, 1, 2, 'Broyage et tri densimétrique', 'Les bouteilles PET sont triées par couleur, broyées puis lavées avant extrusion.', 86, 80, 'completed', 14.20, 'Grade A', 'Rendement de 77 % sur la ligne PET.'],
            [2, 2, 2, 'Désenchevêtrement et compactage', 'Les journaux sont enlevés de leurs films, compactés en ballots et acheminés vers une papeterie.', 79, 72, 'completed', 22.40, 'Grade A', 'Ballots de 500 kg expédiés à Sfax.'],
            [3, 3, 4, 'Tri optique et fusion de verre', 'Le verre est trié optiquement par couleur puis envoyé au four verrier.', 72, 65, 'completed', 27.90, 'Grade B', 'Taux d’impuretés inférieur à 2 %.'],
            [4, 4, 2, 'Lavage et recyclage chimique', 'Rinçage haute pression puis broyage des flacons en flakes pour recyclage.', 64, 58, 'completed', 5.10, 'Grade A', 'Eau de rinçage recyclée sur le site.'],
            [5, 5, 2, 'Fonderie et affinage', 'Fusion des canettes, coulage de lingots puis affinage pour le maintien de la pureté du métal.', 57, 50, 'completed', 8.60, 'Grade A', 'Lingots de 99,7 % d’aluminium.'],
            [6, 6, 5, 'Broyage et compostage contrôlé', 'Broyage des végétaux puis compostage en fosse pendant 60 jours.', 50, 18, 'in_progress', 6.40, 'Grade C', 'Retournement hebdomadaire du tas.'],
            [7, 7, 2, 'Pressage et emballage', 'Les cartons sont pressés, cerclés et préparés pour la filière papier.', 43, 37, 'completed', 24.10, 'Grade A', 'Palletisation aux dimensions européennes normalisées.'],
            [8, 8, 3, 'Diagnostic et reconditionnement', 'Nettoyage du téléphone, remplacement de la batterie et remise en état.', 36, 20, 'in_progress', null, null, 'En attente de la pièce de rechange.'],
            [9, 10, 2, 'Découpage et affinage', 'Tri des pièces métalliques, découpe au chalumeau puis compactage en lingots.', 24, 18, 'completed', 12.80, 'Grade B', 'Acier de récupération à 94 %.'],
            [10, 12, 3, 'Depollution et reconditionnement', 'Effacement du disque dur, remplacement de l’écran et tests fonctionnels.', 15, 5, 'in_progress', 1.20, 'Grade B', 'Satisfaisant pour la revente en reconditionné.'],
            [11, 14, 5, 'Broyage de bois et ennobissement', 'Broyage des palettes, séchage puis pressage en panneaux de particules.', 6, null, 'pending', null, null, 'En attente de la livraison du broyeur.'],
        ];

        $insert = [];

        foreach ($rows as [$id, $wasteId, $responsibleId, $method, $description, $startAgo, $endAgo, $status, $output, $quality, $notes]) {
            $insert[] = [
                'id' => $id,
                'waste_id' => $wasteId,
                'responsible_user_id' => $responsibleId,
                'method' => $method,
                'description' => $description,
                'start_date' => $now->copy()->subDays($startAgo),
                'end_date' => is_null($endAgo) ? null : $now->copy()->subDays($endAgo),
                'status' => $status,
                'output_quantity' => $output,
                'output_quality' => $quality,
                'notes' => $notes,
                'created_at' => $now->copy()->subDays($startAgo + 1),
                'updated_at' => $now->copy()->subDays(max(0, $endAgo ?? 0)),
            ];
        }

        DB::table('recycling_processes')->insert($insert);
    }
}
