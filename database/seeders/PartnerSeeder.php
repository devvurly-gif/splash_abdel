<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clients
        $customers = [
            [
                'type' => 'customer',
                'name' => 'ABC Corporation',
                'legal_name' => 'ABC Corporation SARL',
                'tax_id' => 'TAX-123456',
                'email' => 'contact@abccorp.com',
                'phone' => '+33 1 23 45 67 89',
                'address' => '123 Rue de la République',
                'city' => 'Paris',
                'postal_code' => '75001',
                'country' => 'France',
                'payment_terms' => 'Net 30',
                'credit_limit' => 50000.00,
                'status' => true,
            ],
            [
                'type' => 'customer',
                'name' => 'XYZ Industries',
                'legal_name' => 'XYZ Industries SA',
                'tax_id' => 'TAX-789012',
                'email' => 'info@xyzind.com',
                'phone' => '+33 1 98 76 54 32',
                'address' => '456 Avenue des Champs',
                'city' => 'Lyon',
                'postal_code' => '69001',
                'country' => 'France',
                'payment_terms' => 'Net 15',
                'credit_limit' => 30000.00,
                'status' => true,
            ],
            [
                'type' => 'customer',
                'name' => 'Tech Solutions Ltd',
                'email' => 'sales@techsol.com',
                'phone' => '+33 1 11 22 33 44',
                'address' => '789 Boulevard Saint-Michel',
                'city' => 'Marseille',
                'postal_code' => '13001',
                'country' => 'France',
                'payment_terms' => 'Cash on delivery',
                'credit_limit' => 10000.00,
                'status' => true,
            ],
        ];

        // Fournisseurs
        $suppliers = [
            [
                'type' => 'supplier',
                'name' => 'Global Supplies Inc',
                'legal_name' => 'Global Supplies Inc',
                'tax_id' => 'TAX-345678',
                'email' => 'orders@globalsupplies.com',
                'phone' => '+33 1 55 66 77 88',
                'address' => '321 Industrial Zone',
                'city' => 'Toulouse',
                'postal_code' => '31000',
                'country' => 'France',
                'payment_terms' => 'Net 60',
                'discount_percent' => 5.00,
                'status' => true,
            ],
            [
                'type' => 'supplier',
                'name' => 'Premium Materials Co',
                'legal_name' => 'Premium Materials Company',
                'tax_id' => 'TAX-901234',
                'email' => 'contact@premiummat.com',
                'phone' => '+33 1 44 55 66 77',
                'address' => '654 Manufacturing Street',
                'city' => 'Nantes',
                'postal_code' => '44000',
                'country' => 'France',
                'payment_terms' => 'Net 45',
                'discount_percent' => 3.00,
                'status' => true,
            ],
        ];

        // Tiers qui sont à la fois client et fournisseur
        $both = [
            [
                'type' => 'both',
                'name' => 'Universal Trading',
                'legal_name' => 'Universal Trading SARL',
                'tax_id' => 'TAX-567890',
                'email' => 'info@universaltrading.com',
                'phone' => '+33 1 77 88 99 00',
                'address' => '987 Commerce Avenue',
                'city' => 'Bordeaux',
                'postal_code' => '33000',
                'country' => 'France',
                'payment_terms' => 'Net 30',
                'credit_limit' => 40000.00,
                'discount_percent' => 2.00,
                'status' => true,
            ],
        ];

        foreach (array_merge($customers, $suppliers, $both) as $partnerData) {
            Partner::firstOrCreate(
                ['name' => $partnerData['name']],
                $partnerData
            );
            // Code will be auto-generated: PTN-0001, PTN-0002, etc.
        }
    }
}
