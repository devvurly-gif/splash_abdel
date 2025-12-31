<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NumberingSystem;

class NumberingSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $numberingSystems = [
            // Structure Domain
            [
                'title' => 'Categories Numbering',
                'domain' => 'structure',
                'type' => 'category',
                'template' => 'CAT-{0000}',
                'next_trick' => 1,
                'isActive' => true,
            ],
            [
                'title' => 'Brands Numbering',
                'domain' => 'structure',
                'type' => 'brand',
                'template' => 'BR-{000}',
                'next_trick' => 1,
                'isActive' => true,
            ],
            [
                'title' => 'Products Numbering',
                'domain' => 'structure',
                'type' => 'product',
                'template' => 'PDT-{00000}',
                'next_trick' => 1,
                'isActive' => true,
            ],
            [
                'title' => 'Warehouses Numbering',
                'domain' => 'structure',
                'type' => 'warehouse',
                'template' => 'WH-{0000}',
                'next_trick' => 1,
                'isActive' => true,
            ],
            // Sale Domain
            [
                'title' => 'Sales Invoices Numbering',
                'domain' => 'sale',
                'type' => 'invoice',
                'template' => 'INV-{YY}-{DD}-{0000}',
                'next_trick' => 1,
                'isActive' => true,
            ],
            [
                'title' => 'Sales Orders Numbering',
                'domain' => 'sale',
                'type' => 'order',
                'template' => 'SO-{YY}-{0000}',
                'next_trick' => 1,
                'isActive' => true,
            ],
            [
                'title' => 'Sales Quotations Numbering',
                'domain' => 'sale',
                'type' => 'quotation',
                'template' => 'QT-{YY}-{0000}',
                'next_trick' => 1,
                'isActive' => true,
            ],
            // Purchase Domain
            [
                'title' => 'Purchase Invoices Numbering',
                'domain' => 'purchase',
                'type' => 'invoice',
                'template' => 'PINV-{YY}-{DD}-{0000}',
                'next_trick' => 1,
                'isActive' => true,
            ],
            [
                'title' => 'Purchase Orders Numbering',
                'domain' => 'purchase',
                'type' => 'order',
                'template' => 'PO-{YY}-{0000}',
                'next_trick' => 1,
                'isActive' => true,
            ],
            // Stock Domain
            [
                'title' => 'Stock Transfers Numbering',
                'domain' => 'stock',
                'type' => 'transfer',
                'template' => 'ST-{YY}-{0000}',
                'next_trick' => 1,
                'isActive' => true,
            ],
            [
                'title' => 'Stock Adjustments Numbering',
                'domain' => 'stock',
                'type' => 'adjustment',
                'template' => 'SA-{YY}-{0000}',
                'next_trick' => 1,
                'isActive' => true,
            ],
        ];

        foreach ($numberingSystems as $system) {
            NumberingSystem::updateOrCreate(
                [
                    'domain' => $system['domain'],
                    'type' => $system['type'],
                ],
                $system
            );
        }
    }
}
