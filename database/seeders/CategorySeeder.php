<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create predefined categories
        $categories = [
            [
                'title' => 'Electronics',
                'status' => true,
            ],
            [
                'title' => 'Clothing',
                'status' => true,
            ],
            [
                'title' => 'Food & Beverages',
                'status' => true,
            ],
            [
                'title' => 'Home & Garden',
                'status' => true,
            ],
            [
                'title' => 'Sports & Outdoors',
                'status' => true,
            ],
            [
                'title' => 'Books & Media',
                'status' => true,
            ],
            [
                'title' => 'Health & Beauty',
                'status' => true,
            ],
            [
                'title' => 'Toys & Games',
                'status' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
            // Code will be auto-generated: CAT-0001, CAT-0002, etc.
        }

        // Create additional random categories using factory
      //  Category::factory(10)->create();
    }
}
