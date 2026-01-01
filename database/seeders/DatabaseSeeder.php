<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            NumberingSystemSeeder::class,
            CategorySeeder::class, // Must be after NumberingSystemSeeder
            BrandSeeder::class, // Must be after NumberingSystemSeeder
            WarehouseSeeder::class, // Must be after NumberingSystemSeeder
            PartnerSeeder::class, // Must be after NumberingSystemSeeder
            ProductSeeder::class, // Must be after CategorySeeder and BrandSeeder
            DocumentSeeder::class, // Must be after ProductSeeder, PartnerSeeder, and WarehouseSeeder
        ]);
    }
}
