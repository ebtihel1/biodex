<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Waste;
use App\Models\CollectionPoint;
use App\Models\User;
use App\Models\WasteCategory;
use Illuminate\Support\Facades\DB;

class WasteDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Début du seeding des données de déchets...');

        // Points de collecte existants
        $collectionPoints = CollectionPoint::all();
        $categories = WasteCategory::all();
        $users = User::where('role', 'user')->get();

        if ($users->isEmpty()) {
            $this->command->warn('Aucun utilisateur trouvé. Création d\'un utilisateur par défaut...');
            $users = collect([User::firstOrCreate([
                'email' => 'test@example.com',
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'role' => 'user'
            ])]);
        }

        $totalInserted = 0;

        foreach ($collectionPoints as $point) {
            $this->command->info("Génération de données pour: {$point->name}");

            // Définir un volume de base aléatoire pour chaque point (entre 15 et 40 kg par jour)
            $baseVolume = rand(15, 40);

            // Variation saisonnière (plus élevée en été)
            $seasonalFactor = 1.0;

            for ($day = 0; $day < 90; $day++) {
                $date = now()->subDays(89)->addDays($day);

                // Sauter les week-ends pour certains points (simulation réaliste)
                if ($date->isWeekend() && rand(0, 1) === 0) {
                    continue;
                }

                // Appliquer variation saisonnière
                $month = $date->month;
                if ($month >= 6 && $month <= 8) {
                    $seasonalFactor = 1.3; // Été: plus de déchets
                } elseif ($month >= 12 || $month <= 2) {
                    $seasonalFactor = 0.8; // Hiver: moins de déchets
                } else {
                    $seasonalFactor = 1.0;
                }

                // Variation aléatoire quotidienne (±30%)
                $dailyVariation = 0.7 + (rand(0, 60) / 100.0);

                // Calculer le volume du jour
                $dayVolume = $baseVolume * $seasonalFactor * $dailyVariation;

                // Générer plusieurs entrées par jour (entre 1 et 5 collectes)
                $entriesCount = rand(1, 5);
                $volumes = $this->splitVolume($dayVolume, $entriesCount);

                foreach ($volumes as $volume) {
                    if ($volume < 0.5) continue; // Ignorer les volumes trop petits

                    $category = $categories->random();
                    $user = $users->random();

                    Waste::create([
                        'weight' => round($volume, 2),
                        'collection_point_id' => $point->id,
                        'waste_category_id' => $category->id,
                        'user_id' => $user->id,
                        'description' => "Collecte générée automatiquement",
                        'status' => 'completed',
                        'created_at' => $date->copy()->addHours(rand(8, 18))->addMinutes(rand(0, 59)),
                        'updated_at' => $date->copy()->addHours(rand(8, 18))->addMinutes(rand(0, 59)),
                    ]);

                    $totalInserted++;
                }
            }
        }

        $this->command->info("✅ Seeding terminé: {$totalInserted} enregistrements insérés.");
    }

    /**
     * Divise un volume total en plusieurs parties aléatoires
     */
    private function splitVolume($total, $parts)
    {
        if ($parts <= 1) {
            return [$total];
        }

        $volumes = [];
        $remaining = $total;

        for ($i = 0; $i < $parts - 1; $i++) {
            $maxPart = $remaining / ($parts - $i);
            $part = rand(1, intval($maxPart * 100)) / 100.0;
            $volumes[] = $part;
            $remaining -= $part;
        }

        $volumes[] = $remaining;
        return $volumes;
    }
}
