<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users for inchargeOf
        $users = User::all();

        // Create predefined warehouses
        $warehouses = [
            [
                'title' => 'Main Warehouse',
                'isprincipal' => true,
                'inchargeOf' => $users->first()?->id ?? null,
                'type' => 'warehouse',
            ],
            [
                'title' => 'Secondary Storage',
                'isprincipal' => false,
                'inchargeOf' => $users->skip(1)->first()?->id ?? null,
                'type' => 'warehouse',
            ],
            [
                'title' => 'Retail Store',
                'isprincipal' => false,
                'inchargeOf' => $users->first()?->id ?? null,
                'type' => 'store',
            ],
            [
                'title' => 'Fuel Tank',
                'isprincipal' => false,
                'inchargeOf' => $users->skip(1)->first()?->id ?? null,
                'type' => 'tank',
            ],
            [
                'title' => 'Distribution Center',
                'isprincipal' => false,
                'inchargeOf' => $users->first()?->id ?? null,
                'type' => 'warehouse',
            ],
        ];

        foreach ($warehouses as $warehouseData) {
            Warehouse::firstOrCreate(
                ['title' => $warehouseData['title']],
                $warehouseData
            );
            // Code will be auto-generated: WH-01, WH-02, etc.
        }

        // Create additional random warehouses using factory (5 total)
        if (Warehouse::count() < 5) {
            $remaining = 5 - Warehouse::count();
            Warehouse::factory($remaining)->create();
        }
    }
}

