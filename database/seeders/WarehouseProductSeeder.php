<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Database\Seeder;

class WarehouseProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = Warehouse::all();
        $products = Product::all();

        if ($warehouses->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No warehouses or products found. Skipping warehouse-product relationships.');
            return;
        }

        // Attach products to warehouses with quantity and cmup
        foreach ($warehouses as $warehouse) {
            // Each warehouse gets a random number of products (between 3 and all products)
            $numberOfProducts = min(rand(3, $products->count()), $products->count());
            
            // Get random products
            $randomProducts = $products->random($numberOfProducts);
            
            // Prepare sync data with quantity and cmup
            $syncData = [];
            foreach ($randomProducts as $product) {
                $syncData[$product->id] = [
                    'quantity' => rand(10, 500), // Random quantity between 10 and 500
                    'cmup' => round($product->purchase_price * (1 + rand(-10, 20) / 100), 2), // CMUP based on purchase price with variation
                ];
            }
            
            // Attach products to warehouse (without detaching existing)
            $warehouse->products()->syncWithoutDetaching($syncData);
        }

        // Ensure some products are in multiple warehouses
        // Get a few products and add them to additional warehouses
        $someProducts = $products->take(min(5, $products->count()));
        foreach ($someProducts as $product) {
            // Add this product to 2-3 random warehouses
            $additionalWarehouses = $warehouses->random(min(rand(2, 3), $warehouses->count()));
            foreach ($additionalWarehouses as $warehouse) {
                // Check if product is already in this warehouse
                if (!$warehouse->products()->where('product_id', $product->id)->exists()) {
                    $warehouse->products()->attach($product->id, [
                        'quantity' => rand(10, 500),
                        'cmup' => round($product->purchase_price * (1 + rand(-10, 20) / 100), 2),
                    ]);
                }
            }
        }

        $this->command->info('Seeded warehouse-product relationships.');
    }
}
