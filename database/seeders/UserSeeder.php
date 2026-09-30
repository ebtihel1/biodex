<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');
        $now = Carbon::now();

        $rows = [
            // Administrateurs
            [1, 'Amine Ben Salah', 'admin@biodex.tn', 3, '+216 20 145 782', '15 rue de la Liberté', 'Tunis', 210],
            [2, 'Karim Zribi', 'karim.zribi@biodex.tn', 3, '+216 24 908 336', 'Zone industrielle Charguia II, lot 42', 'Tunis', 205],
            [3, 'Salma Trabelsi', 'salma.trabelsi@biodex.tn', 3, '+216 22 631 470', 'Centre El Menzah 4, bloc C', 'Tunis', 200],

            // Entreprises
            [4, 'Sami Gharbi', 'contact@ecoverde.tn', 2, '+216 74 320 118', 'Zone industrielle Sidi Rezig, rue 12', 'Sousse', 180],
            [5, 'Nour Ben Youssef', 'direction@greenpack.tn', 2, '+216 71 940 552', 'Rue du Lac Turkana, Les Berges du Lac 2', 'Tunis', 175],
            [6, 'Ahmed Sassi', 'ahmed.sassi@ecopack.tn', 2, '+216 76 205 887', 'Route de Monastir km 3.5', 'Monastir', 165],

            // Citoyens
            [7, 'Lina Bouazizi', 'lina.bouazizi@gmail.com', 1, '+216 25 431 902', '30 avenue Habib Bourguiba', 'Tunis', 160],
            [8, 'Yassine Chebbi', 'yassine.chebbi@outlook.com', 1, '+216 20 774 615', 'Cited El Khadra, rue des Orangers', 'Ariana', 155],
            [9, 'Mariem Jaziri', 'mariem.jaziri@gmail.com', 1, '+216 23 118 344', 'Avenue Taieb Mhiri 45', 'Sousse', 150],
            [10, 'Ines Ferchichi', 'ines.ferchichi@gmail.com', 1, '+216 27 902 118', 'Rue de la Republique 8', 'Nabeul', 145],
            [11, 'Wael Mansouri', 'wael.mansouri@yahoo.fr', 1, '+216 21 663 907', 'Residence Al Qods, appartement 12', 'Tunis', 138],
            [12, 'Rania Hammami', 'rania.hammami@gmail.com', 1, '+216 26 447 231', 'Avenue de la Station 19', 'Hammamet', 132],
            [13, 'Houssem Bouazizi', 'houssem.bouazizi@outlook.com', 1, '+216 24 155 860', 'Cited Menzel Bourguiba, impasse 3', 'Bizerte', 126],
            [14, 'Sonia Chtioui', 'sonia.chtioui@gmail.com', 1, '+216 22 733 019', 'Rue du Port 21', 'Mahdia', 120],
        ];

        $insert = [];

        foreach ($rows as [$id, $name, $email, $roleId, $phone, $address, $city, $daysAgo]) {
            $createdAt = $now->copy()->subDays($daysAgo);

            $insert[] = [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'email_verified_at' => $createdAt->copy()->addDay(),
                'password' => $password,
                'role_id' => $roleId,
                'phone' => $phone,
                'address' => $address,
                'city' => $city,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(3),
            ];
        }

        DB::table('users')->upsert(
            $insert,
            ['id'],
            [
                'name',
                'email',
                'email_verified_at',
                'password',
                'role_id',
                'phone',
                'address',
                'city',
                'created_at',
                'updated_at',
            ],
        );

        // Un utilisateur connecte via Google (avatar externe)
        DB::table('users')->where('id', 14)->update([
            'google_id' => '109823456789123456781',
            'avatar' => 'https://lh3.googleusercontent.com/a/biodex-sonia-chtioui',
        ]);
    }
}
