<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::insert([
            ['name' => 'citoyen', 'description' => 'Utilisateur particulier'],
            ['name' => 'entreprise', 'description' => 'Compte entreprise ou collectivité'],
            ['name' => 'admin', 'description' => 'Administrateur de la plateforme'],
        ]);
    }
}
