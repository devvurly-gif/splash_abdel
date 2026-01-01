<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create predefined warehouses
        $warehouses = [
            [
                'title' => 'Main Warehouse',
                'status' => true,
            ],
            [
                'title' => 'Secondary Warehouse',
                'status' => true,
            ],
            [
                'title' => 'Distribution Center',
                'status' => true,
            ],
            [
                'title' => 'Retail Store Warehouse',
                'status' => true,
            ],
            [
                'title' => 'Cold Storage Warehouse',
                'status' => true,
            ],
        ];

        foreach ($warehouses as $warehouseData) {
            Warehouse::firstOrCreate(
                ['title' => $warehouseData['title']],
                $warehouseData
            );
            // Code will be auto-generated: WH-0001, WH-0002, etc.
        }
    }
}

