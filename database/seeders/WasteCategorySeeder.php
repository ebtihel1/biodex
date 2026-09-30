<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WasteCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('waste_categories')->insert([
            [
                'id' => 1,
                'name' => 'plastique',
                'description' => 'hjkm',
                'recycling_instructions' => 'jjj',
                'created_at' => '2025-10-02 15:11:04',
                'updated_at' => '2025-10-14 14:42:02',
            ],
            [
                'id' => 2,
                'name' => 'Glass',
                'description' => 'glases',
                'recycling_instructions' => 'non',
                'created_at' => '2025-10-14 13:14:49',
                'updated_at' => '2025-10-14 13:14:49',
            ],
            [
                'id' => 3,
                'name' => 'Metal',
                'description' => 'metal',
                'recycling_instructions' => 'hdid',
                'created_at' => '2025-10-17 10:24:39',
                'updated_at' => '2025-10-17 10:24:39',
            ],
        ]);
    }
}
