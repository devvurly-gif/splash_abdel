<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create predefined brands
        $brands = [
            [
                'title' => 'Apple',
                'status' => true,
            ],
            [
                'title' => 'Samsung',
                'status' => true,
            ],
            [
                'title' => 'Nike',
                'status' => true,
            ],
            [
                'title' => 'Adidas',
                'status' => true,
            ],
            [
                'title' => 'Sony',
                'status' => true,
            ],
            [
                'title' => 'Microsoft',
                'status' => true,
            ],
            [
                'title' => 'LG',
                'status' => true,
            ],
            [
                'title' => 'Canon',
                'status' => true,
            ],
        ];

        foreach ($brands as $brandData) {
            Brand::create($brandData);
            // Code will be auto-generated: BR-001, BR-002, etc.
        }

        // Create additional random brands using factory
        // Brand::factory(10)->create();
    }
}

