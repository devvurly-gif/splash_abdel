<?php

namespace Database\Seeders;

use App\Models\DocumentHeader;
use App\Models\Partner;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    protected $documentService;

    public function __construct()
    {
        $this->documentService = app(DocumentService::class);
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first user for created_by
        $user = User::first();
        if (!$user) {
            $this->command->warn('No user found. Please run UserSeeder first.');
            return;
        }

        // Get warehouses
        $warehouses = Warehouse::where('status', true)->get();
        if ($warehouses->isEmpty()) {
            $this->command->warn('No warehouses found. Please run WarehouseSeeder first.');
            return;
        }

        // Get customers
        $customers = Partner::customers()->where('status', true)->get();
        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Please run PartnerSeeder first.');
            return;
        }

        // Get suppliers
        $suppliers = Partner::suppliers()->where('status', true)->get();
        if ($suppliers->isEmpty()) {
            $this->command->warn('No suppliers found. Please run PartnerSeeder first.');
            return;
        }

        // Get products
        $products = Product::active()->get();
        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please create products first.');
            return;
        }

        // Set authenticated user for document creation
        auth()->login($user);

        // Create 100 mixed documents
        $this->createMixedDocuments($customers, $suppliers, $warehouses, $products, 100);

        auth()->logout();
    }

    protected function createMixedDocuments($customers, $suppliers, $warehouses, $products, $count = 100)
    {
        $documentTypes = [
            // Sale documents
            ['domain' => 'sale', 'type' => 'invoice', 'needs_partner' => true, 'partner_type' => 'customer'],
            ['domain' => 'sale', 'type' => 'delivery_note', 'needs_partner' => true, 'partner_type' => 'customer'],
            ['domain' => 'sale', 'type' => 'return', 'needs_partner' => true, 'partner_type' => 'customer'],
            
            // Purchase documents
            ['domain' => 'purchase', 'type' => 'invoice', 'needs_partner' => true, 'partner_type' => 'supplier'],
            ['domain' => 'purchase', 'type' => 'receipt', 'needs_partner' => true, 'partner_type' => 'supplier'],
            ['domain' => 'purchase', 'type' => 'return', 'needs_partner' => true, 'partner_type' => 'supplier'],
            
            // Stock documents
            ['domain' => 'stock', 'type' => 'adjustment', 'needs_partner' => false],
            ['domain' => 'stock', 'type' => 'manual_entry', 'needs_partner' => false],
            ['domain' => 'stock', 'type' => 'manual_exit', 'needs_partner' => false],
        ];

        $created = 0;
        $skipped = 0;
        $validated = 0;

        $this->command->info("Creating {$count} mixed documents...");

        for ($i = 1; $i <= $count; $i++) {
            // Select random document type
            $docType = $documentTypes[array_rand($documentTypes)];
            
            // Get partner if needed
            $partnerId = null;
            if ($docType['needs_partner']) {
                if ($docType['partner_type'] === 'customer') {
                    $partnerId = $customers->random()->id;
                } else {
                    $partnerId = $suppliers->random()->id;
                }
            }

            // Generate random date (between 60 days ago and today)
            $daysAgo = rand(0, 60);
            $documentDate = now()->subDays($daysAgo);
            $dueDate = null;
            
            // Set due date for invoices
            if (in_array($docType['type'], ['invoice'])) {
                $dueDate = $documentDate->copy()->addDays(rand(15, 45));
            }

            // Random number of lines (1 to 5)
            $lineCount = rand(1, 5);
            $lines = [];
            
            for ($j = 0; $j < $lineCount; $j++) {
                $product = $products->random();
                
                // For adjustments, quantity can be negative (decrease) or positive (increase)
                if ($docType['type'] === 'adjustment') {
                    $quantity = rand(-30, 30); // Can be negative or positive
                } elseif ($docType['type'] === 'manual_exit') {
                    $quantity = -rand(1, 50); // Always negative for exits
                } else {
                    $quantity = rand(1, 50); // Always positive for entries
                }
                
                // Determine unit price based on domain
                if ($docType['domain'] === 'purchase') {
                    $unitPrice = $product->purchase_price ?? rand(20, 200);
                    $unitCost = $unitPrice;
                } elseif ($docType['domain'] === 'sale') {
                    $unitPrice = $product->sale_price ?? rand(50, 500);
                    $unitCost = $product->purchase_price ?? rand(20, 200);
                } else {
                    // Stock documents
                    $unitPrice = 0;
                    $unitCost = $product->purchase_price ?? rand(20, 200);
                }
                
                $lines[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'unit_cost' => $unitCost,
                    'discount_percent' => $docType['domain'] === 'stock' ? 0 : rand(0, 15),
                    'tax_percent' => $docType['domain'] === 'stock' ? 0 : rand(0, 25),
                ];
            }

            try {
                $document = $this->documentService->createDocument([
                    'domain' => $docType['domain'],
                    'type' => $docType['type'],
                    'partner_id' => $partnerId,
                    'warehouse_id' => $warehouses->random()->id,
                    'document_date' => $documentDate->format('Y-m-d'),
                    'due_date' => $dueDate ? $dueDate->format('Y-m-d') : null,
                    'reference' => 'REF-' . strtoupper($docType['domain']) . '-' . strtoupper($docType['type']) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'notes' => 'Document généré automatiquement #' . $i . ' - ' . ucfirst($docType['domain']) . ' ' . $docType['type'],
                    'lines' => $lines,
                ]);
                $created++;
                
                // Validate 75% of documents to create stock movements
                // Older documents are more likely to be validated
                $shouldValidate = false;
                if ($daysAgo > 7) {
                    // Documents older than 7 days: 90% chance to validate
                    $shouldValidate = rand(1, 100) <= 90;
                } else {
                    // Recent documents: 60% chance to validate
                    $shouldValidate = rand(1, 100) <= 60;
                }
                
                if ($shouldValidate) {
                    try {
                        $this->documentService->validateDocument($document->id);
                        $validated++;
                    } catch (\Exception $e) {
                        $this->command->warn("Failed to validate document #{$i}: " . $e->getMessage());
                    }
                }
                
                // Progress indicator
                if ($i % 10 === 0) {
                    $this->command->info("Progress: {$i}/{$count} documents created, {$validated} validated...");
                }
            } catch (\Exception $e) {
                $skipped++;
                $this->command->warn("Failed to create document #{$i}: " . $e->getMessage());
            }
        }

        $this->command->info("✓ Created {$created} documents, validated {$validated}, skipped {$skipped}");
        $this->command->info("Documents distribution:");
        $this->command->info("  - Sale: " . DocumentHeader::where('domain', 'sale')->count());
        $this->command->info("  - Purchase: " . DocumentHeader::where('domain', 'purchase')->count());
        $this->command->info("  - Stock: " . DocumentHeader::where('domain', 'stock')->count());
        
        // Count stock movements created
        $movementCount = \App\Models\JournalStock::count();
        $this->command->info("✓ Created {$movementCount} stock movements");
    }
}
