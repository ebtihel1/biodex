<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WasteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('wastes')->insert([
            [
                'id' => 1,
                'type' => 'Papier',
                'weight' => 15.00,
                'status' => 'recyclable',
                'user_id' => 2,
                'waste_category_id' => 1,
                'collection_point_id' => 2,
                'image_path' => 'wastes/u3JQdJDNR0oZcxLHHg7okRyiQRm1BLW9Vw5F6FlF.jpg',
                'description' => 'jjjjj',
                'created_at' => '2025-10-02 15:12:10',
                'updated_at' => '2025-10-02 15:16:18',
            ],
            [
                'id' => 2,
                'type' => 'Qui anim aut sit et',
                'weight' => 1.00,
                'status' => 'recyclable',
                'user_id' => 2,
                'waste_category_id' => 1,
                'collection_point_id' => 1,
                'image_path' => 'wastes/QRhmsO5cClCfWieHQuXEu9GK3OkMdGawkoZrIAoQ.jpg',
                'description' => 'hhh',
                'created_at' => '2025-10-14 14:31:52',
                'updated_at' => '2025-10-14 14:31:52',
            ],
            [
                'id' => 3,
                'type' => 'Plastic bottle',
                'weight' => 0.01,
                'status' => 'reusable',
                'user_id' => 2,
                'waste_category_id' => 1,
                'collection_point_id' => 2,
                'image_path' => 'wastes/E0sUhlbfSgf3lqNwygfSTAgBDQzdZn5j31B6LI5M.jpg',
                'description' => 'hhh',
                'created_at' => '2025-10-14 18:04:21',
                'updated_at' => '2025-10-14 18:04:21',
            ],
            [
                'id' => 4,
                'type' => 'pl',
                'weight' => 15.00,
                'status' => 'recyclable',
                'user_id' => 2,
                'waste_category_id' => 1,
                'collection_point_id' => 1,
                'image_path' => 'wastes/RoZzOmDPuCAavMQK3Zw39y1MypRPCBJ2wr1Da3Dx.jpg',
                'description' => 'hhhhhhhhhh',
                'created_at' => '2025-10-17 20:15:01',
                'updated_at' => '2025-10-17 20:15:01',
            ],
        ]);
    }
}
