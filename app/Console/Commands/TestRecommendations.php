<?php

namespace App\Console\Commands;

use App\Services\RecommendationTester;
use Illuminate\Console\Command;

class TestRecommendations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recommendations:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Teste le système de recommandation Apriori';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧪 Démarrage des tests du système de recommandation...');

        $tester = new RecommendationTester();
        $results = $tester->test();

        // Afficher les résultats
        $tester->displayResults($results);

        // Logger les résultats
        \Log::info('Recommendation system test completed', $results);

        $this->info('✅ Tests terminés !');

        return Command::SUCCESS;
    }
}
