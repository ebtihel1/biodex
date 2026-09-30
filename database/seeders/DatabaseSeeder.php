<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Tables de l'application, dans l'ordre inverse des dependances,
     * afin de pouvoir vider la base avant de la re-remplir.
     *
     * @var array<int, string>
     */
    protected array $applicationTables = [
        'notifications',
        'participations',
        'reservations',
        'orders',
        'bookings',
        'donations',
        'products',
        'recycling_processes',
        'wastes',
        'campaigns',
        'repair_services',
        'collection_points',
        'waste_categories',
        'users',
        'roles',
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->clearApplicationData();

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            WasteCategorySeeder::class,
            CollectionPointSeeder::class,
            WasteSeeder::class,
            RecyclingProcessSeeder::class,
            ProductSeeder::class,
            RepairServiceSeeder::class,
            DonationSeeder::class,
            OrderSeeder::class,
            ReservationSeeder::class,
            BookingSeeder::class,
            NotificationSeeder::class,
            CampaignSeeder::class,
            ParticipationSeeder::class,
        ]);
    }

    /**
     * Vide les tables métier pour que le seeder puisse être relance
     * autant de fois que necessaire (db:seed).
     */
    protected function clearApplicationData(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        }

        foreach ($this->applicationTables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->delete();
            }
        }

        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }
}
