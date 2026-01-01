<?php

namespace App\Services;

use App\Models\JournalStock;
use App\Models\StockBalance;
use App\Models\DocumentHeader;
use App\Models\DocumentLine;
use App\Models\NumberingSystem;
use Illuminate\Support\Facades\DB;

class JournalStockService
{
    /**
     * Créer une entrée dans le journal depuis un document
     */
    public function createFromDocument(
        DocumentHeader $document, 
        DocumentLine $item, 
        string $movementType
    ): JournalStock {
        $warehouseId = $this->getWarehouseId($document, $movementType);
        $quantity = $this->getQuantity($movementType, $item);
        $unitCost = $item->unit_cost ?? $this->getProductCost($item->product_id, $warehouseId);
        
        return JournalStock::create([
            'code' => $this->generateCode(),
            'movement_type' => $movementType,
            'document_header_id' => $document->id,
            'document_line_id' => $item->id,
            'warehouse_id' => $warehouseId,
            'product_id' => $item->product_id,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => abs($quantity) * $unitCost,
            'movement_date' => $document->document_date,
            'reference' => $document->code,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Créer un mouvement de transfert (sortie + entrée)
     */
    public function createTransfer(DocumentHeader $transfer, DocumentLine $item): void
    {
        // Récupérer les warehouses depuis le document
        // Note: Pour les transferts, vous devrez adapter selon votre structure
        // Ici on suppose que related_entity_id contient le to_warehouse_id
        $fromWarehouseId = $transfer->warehouse_id;
        $toWarehouseId = $transfer->related_entity_id; // ou créer une colonne to_warehouse_id
        
        $unitCost = $item->unit_cost ?? $this->getProductCost($item->product_id, $fromWarehouseId);

        // Sortie du dépôt source
        JournalStock::create([
            'code' => $this->generateCode(),
            'movement_type' => 'transfer_out',
            'document_header_id' => $transfer->id,
            'document_line_id' => $item->id,
            'warehouse_id' => $fromWarehouseId,
            'product_id' => $item->product_id,
            'quantity' => -abs($item->quantity),
            'unit_cost' => $unitCost,
            'total_cost' => $item->quantity * $unitCost,
            'movement_date' => $transfer->document_date,
            'reference' => $transfer->code,
            'created_by' => auth()->id(),
        ]);

        // Entrée dans le dépôt destination
        JournalStock::create([
            'code' => $this->generateCode(),
            'movement_type' => 'transfer_in',
            'document_header_id' => $transfer->id,
            'document_line_id' => $item->id,
            'warehouse_id' => $toWarehouseId,
            'product_id' => $item->product_id,
            'quantity' => abs($item->quantity),
            'unit_cost' => $unitCost,
            'total_cost' => $item->quantity * $unitCost,
            'movement_date' => $transfer->document_date,
            'reference' => $transfer->code,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Mettre à jour le stock balance après un mouvement
     */
    public function updateStockBalance(int $warehouseId, int $productId): void
    {
        $balance = StockBalance::firstOrCreate([
            'warehouse_id' => $warehouseId,
            'product_id' => $productId,
        ]);

        $balance->updateFromMovements();
    }

    /**
     * Obtenir le stock disponible
     */
    public function getStockBalance(int $warehouseId, int $productId): StockBalance
    {
        $balance = StockBalance::firstOrCreate([
            'warehouse_id' => $warehouseId,
            'product_id' => $productId,
        ]);

        // S'assurer que le stock est à jour
        $balance->updateFromMovements();

        return $balance;
    }

    /**
     * Obtenir l'historique des mouvements
     */
    public function getStockHistory(int $warehouseId, int $productId, $dateFrom = null, $dateTo = null)
    {
        $query = JournalStock::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId);

        if ($dateFrom) {
            $query->where('movement_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('movement_date', '<=', $dateTo);
        }

        return $query->orderBy('movement_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Vérifier le stock disponible
     */
    public function checkStockAvailability(int $warehouseId, int $productId, float $requiredQuantity): bool
    {
        $balance = $this->getStockBalance($warehouseId, $productId);
        return $balance->available_quantity >= $requiredQuantity;
    }

    private function getWarehouseId(DocumentHeader $document, string $movementType): ?int
    {
        return $document->warehouse_id;
    }

    private function getQuantity(string $movementType, DocumentLine $item): float
    {
        $entryTypes = [
            'purchase_invoice', 
            'purchase_receipt', 
            'transfer_in', 
            'adjustment_increase', 
            'manual_entry',
            'sale_return' // Retour client = entrée
        ];
        
        $exitTypes = [
            'sale_invoice',
            'sale_delivery',
            'purchase_return',
            'transfer_out',
            'adjustment_decrease',
            'manual_exit'
        ];
        
        // For adjustments and manual movements, preserve the sign from the line
        if (in_array($movementType, ['adjustment_increase', 'adjustment_decrease', 'manual_entry', 'manual_exit'])) {
            return $item->quantity; // Keep original sign
        }
        
        // For other types, determine based on movement type
        $quantity = abs($item->quantity);
        
        return in_array($movementType, $entryTypes) 
            ? $quantity 
            : -$quantity; // Sortie = négatif
    }

    private function getProductCost(int $productId, int $warehouseId): float
    {
        // Récupérer le dernier coût d'entrée pour ce produit dans ce dépôt
        $lastEntry = JournalStock::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->entries()
            ->latest('movement_date')
            ->first();

        return $lastEntry ? $lastEntry->unit_cost : 0;
    }

    private function generateCode(): string
    {
        $numberingSystem = NumberingSystem::byDomainAndType(
            NumberingSystem::DOMAIN_STOCK,
            'journal_stock'
        )->active()->first();

        if (!$numberingSystem) {
            return 'JST-' . now()->format('Y') . '-' . str_pad(
                JournalStock::count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );
        }

        return $numberingSystem->getNextNumber();
    }
}

