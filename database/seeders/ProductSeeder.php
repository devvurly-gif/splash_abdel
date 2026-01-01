<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::where('status', true)->get();
        $brands = Brand::where('status', true)->get();

        if ($categories->isEmpty() || $brands->isEmpty()) {
            $this->command->warn('Categories or Brands not found. Please run CategorySeeder and BrandSeeder first.');
            return;
        }

        $products = [
            [
                'name' => 'Laptop Dell XPS 15',
                'description' => 'High-performance laptop with 16GB RAM and 512GB SSD',
                'category_id' => $categories->where('title', 'Electronics')->first()?->id ?? $categories->random()->id,
                'brand_id' => $brands->random()->id,
                'purchase_price' => 800.00,
                'sale_price' => 1200.00,
                'min_stock' => 5,
                'max_stock' => 50,
                'unit' => 'piece',
                'status' => true,
            ],
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'Latest iPhone with advanced features',
                'category_id' => $categories->where('title', 'Electronics')->first()?->id ?? $categories->random()->id,
                'brand_id' => $brands->where('title', 'Apple')->first()?->id ?? $brands->random()->id,
                'purchase_price' => 900.00,
                'sale_price' => 1300.00,
                'min_stock' => 10,
                'max_stock' => 100,
                'unit' => 'piece',
                'status' => true,
            ],
            [
                'name' => 'Nike Air Max 270',
                'description' => 'Comfortable running shoes',
                'category_id' => $categories->where('title', 'Clothing')->first()?->id ?? $categories->random()->id,
                'brand_id' => $brands->where('title', 'Nike')->first()?->id ?? $brands->random()->id,
                'purchase_price' => 60.00,
                'sale_price' => 120.00,
                'min_stock' => 20,
                'max_stock' => 200,
                'unit' => 'pair',
                'status' => true,
            ],
            [
                'name' => 'Samsung 55" 4K TV',
                'description' => 'Ultra HD Smart TV with HDR',
                'category_id' => $categories->where('title', 'Electronics')->first()?->id ?? $categories->random()->id,
                'brand_id' => $brands->where('title', 'Samsung')->first()?->id ?? $brands->random()->id,
                'purchase_price' => 500.00,
                'sale_price' => 800.00,
                'min_stock' => 3,
                'max_stock' => 30,
                'unit' => 'piece',
                'status' => true,
            ],
            [
                'name' => 'Office Desk Chair',
                'description' => 'Ergonomic office chair with lumbar support',
                'category_id' => $categories->where('title', 'Home & Garden')->first()?->id ?? $categories->random()->id,
                'brand_id' => $brands->random()->id,
                'purchase_price' => 80.00,
                'sale_price' => 150.00,
                'min_stock' => 10,
                'max_stock' => 50,
                'unit' => 'piece',
                'status' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['name' => $productData['name']],
                $productData
            );
            // Code will be auto-generated: PDT-00001, PDT-00002, etc.
        }

        $this->command->info('Created ' . count($products) . ' products');
    }
}
