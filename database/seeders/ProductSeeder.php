<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
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
                'ean13' => '1234567890123',
                'description' => 'High-performance laptop with 16GB RAM and 512GB SSD',
                'category_id' => $categories->where('title', 'Electronics')->first()?->id ?? $categories->random()->id,
                'brand_id' => $brands->random()->id,
                'purchase_price' => 800.00,
                'sale_price' => 1200.00,
                'unit' => 'piece',
                'isactive' => true,
                'onPromo' => false,
                'isFeatured' => true,
            ],
            [
                'name' => 'iPhone 15 Pro',
                'ean13' => '2345678901234',
                'description' => 'Latest iPhone with advanced features',
                'category_id' => $categories->where('title', 'Electronics')->first()?->id ?? $categories->random()->id,
                'brand_id' => $brands->where('title', 'Apple')->first()?->id ?? $brands->random()->id,
                'purchase_price' => 900.00,
                'sale_price' => 1300.00,
                'unit' => 'piece',
                'isactive' => true,
                'onPromo' => true,
                'isFeatured' => true,
            ],
            [
                'name' => 'Nike Air Max 270',
                'ean13' => '3456789012345',
                'description' => 'Comfortable running shoes',
                'category_id' => $categories->where('title', 'Clothing')->first()?->id ?? $categories->random()->id,
                'brand_id' => $brands->where('title', 'Nike')->first()?->id ?? $brands->random()->id,
                'purchase_price' => 60.00,
                'sale_price' => 120.00,
                'unit' => 'pair',
                'isactive' => true,
                'onPromo' => false,
                'isFeatured' => false,
            ],
            [
                'name' => 'Samsung 55" 4K TV',
                'ean13' => '4567890123456',
                'description' => 'Ultra HD Smart TV with HDR',
                'category_id' => $categories->where('title', 'Electronics')->first()?->id ?? $categories->random()->id,
                'brand_id' => $brands->where('title', 'Samsung')->first()?->id ?? $brands->random()->id,
                'purchase_price' => 500.00,
                'sale_price' => 800.00,
                'unit' => 'piece',
                'isactive' => true,
                'onPromo' => true,
                'isFeatured' => false,
            ],
            [
                'name' => 'Office Desk Chair',
                'ean13' => '5678901234567',
                'description' => 'Ergonomic office chair with lumbar support',
                'category_id' => $categories->where('title', 'Home & Garden')->first()?->id ?? $categories->random()->id,
                'brand_id' => $brands->random()->id,
                'purchase_price' => 80.00,
                'sale_price' => 150.00,
                'unit' => 'piece',
                'isactive' => true,
                'onPromo' => false,
                'isFeatured' => false,
            ],
            [
                'name' => 'Wireless Mouse Logitech MX Master 3',
                'ean13' => '6789012345678',
                'description' => 'Premium wireless mouse with advanced tracking',
                'category_id' => $categories->where('title', 'Electronics')->first()?->id ?? $categories->random()->id,
                'brand_id' => $brands->random()->id,
                'purchase_price' => 50.00,
                'sale_price' => 99.00,
                'unit' => 'piece',
                'isactive' => true,
                'onPromo' => false,
                'isFeatured' => false,
            ],
            [
                'name' => 'Mechanical Keyboard RGB',
                'ean13' => '7890123456789',
                'description' => 'Gaming keyboard with RGB backlight',
                'category_id' => $categories->where('title', 'Electronics')->first()?->id ?? $categories->random()->id,
                'brand_id' => $brands->random()->id,
                'purchase_price' => 70.00,
                'sale_price' => 129.00,
                'unit' => 'piece',
                'isactive' => true,
                'onPromo' => true,
                'isFeatured' => false,
            ],
            [
                'name' => 'Coffee Maker Deluxe',
                'ean13' => '8901234567890',
                'description' => 'Programmable coffee maker with thermal carafe',
                'category_id' => $categories->where('title', 'Home & Garden')->first()?->id ?? $categories->random()->id,
                'brand_id' => $brands->random()->id,
                'purchase_price' => 120.00,
                'sale_price' => 199.00,
                'unit' => 'piece',
                'isactive' => true,
                'onPromo' => false,
                'isFeatured' => true,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::firstOrCreate(
                ['name' => $productData['name']],
                $productData
            );
            // Code will be auto-generated: PDT-00001, PDT-00002, etc.
            
            // Optionally create a placeholder image for each product
            // You can uncomment this if you want to seed images
            /*
            if ($product->images()->count() === 0) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'title' => $product->name,
                    'url' => 'https://via.placeholder.com/400x400?text=' . urlencode($product->name),
                    'alt' => $product->name,
                    'isprimary' => true,
                    'order' => 0,
                ]);
            }
            */
        }

        $this->command->info('Created/Updated ' . count($products) . ' products');
    }
}
