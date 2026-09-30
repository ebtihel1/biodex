<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('roles')->insert([
            [
                'id' => 1,
                'name' => 'citoyen',
                'description' => 'Utilisateur particulier déposant des déchets ou achetant des produits recyclés',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'entreprise',
                'description' => 'Entreprise, collectivité ou association agissant dans le tri et la logistique',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'admin',
                'description' => 'Administrateur de la plateforme Biodex',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
