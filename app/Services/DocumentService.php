<?php

namespace App\Services;

use App\Models\DocumentHeader;
use App\Models\DocumentLine;
use Illuminate\Support\Facades\DB;

class DocumentService
{
    /**
     * Créer un document complet (header + lines)
     */
    public function createDocument(array $data): DocumentHeader
    {
        return DB::transaction(function () use ($data) {
            // Créer l'en-tête
            $header = DocumentHeader::create([
                'domain' => $data['domain'],
                'type' => $data['type'],
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'partner_id' => $data['partner_id'] ?? null,
                'related_entity_type' => $data['related_entity_type'] ?? null,
                'related_entity_id' => $data['related_entity_id'] ?? null,
                'document_date' => $data['document_date'],
                'due_date' => $data['due_date'] ?? null,
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
                'reference' => $data['reference'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Créer les lignes
            $lineNumber = 1;
            $subtotal = 0;
            
            foreach ($data['lines'] as $lineData) {
                $line = DocumentLine::create([
                    'document_header_id' => $header->id,
                    'product_id' => $lineData['product_id'],
                    'line_number' => $lineNumber++,
                    'quantity' => $lineData['quantity'],
                    'unit_price' => $lineData['unit_price'],
                    'unit_cost' => $lineData['unit_cost'] ?? null,
                    'discount_percent' => $lineData['discount_percent'] ?? 0,
                    'tax_percent' => $lineData['tax_percent'] ?? 0,
                    'description' => $lineData['description'] ?? null,
                    'notes' => $lineData['notes'] ?? null,
                ]);

                $subtotal += $line->line_total;
            }

            // Mettre à jour les totaux de l'en-tête
            $header->subtotal = $subtotal;
            $header->total_amount = $subtotal; // + taxes globales si nécessaire
            $header->save();

            return $header->load('lines');
        });
    }

    /**
     * Valider un document (créer les mouvements de stock)
     */
    public function validateDocument(int $documentId): DocumentHeader
    {
        $document = DocumentHeader::with('lines')->findOrFail($documentId);
        
        if ($document->status !== 'draft') {
            throw new \Exception('Document déjà validé ou annulé');
        }

        DB::transaction(function () use ($document) {
            $document->validate();
        });

        return $document->fresh();
    }

    /**
     * Annuler un document
     */
    public function cancelDocument(int $documentId): DocumentHeader
    {
        $document = DocumentHeader::findOrFail($documentId);
        
        if ($document->status === 'cancelled') {
            throw new \Exception('Document déjà annulé');
        }

        $document->status = 'cancelled';
        $document->save();

        // Optionnel: créer des mouvements inverses dans le journal
        // pour annuler l'impact sur le stock

        return $document;
    }

    /**
     * Mettre à jour un document
     */
    public function updateDocument(int $documentId, array $data): DocumentHeader
    {
        $document = DocumentHeader::with('lines')->findOrFail($documentId);
        
        if ($document->status !== 'draft') {
            throw new \Exception('Seuls les documents en brouillon peuvent être modifiés');
        }

        return DB::transaction(function () use ($document, $data) {
            // Mettre à jour l'en-tête
            $document->update([
                'warehouse_id' => $data['warehouse_id'] ?? $document->warehouse_id,
                'partner_id' => $data['partner_id'] ?? $document->partner_id,
                'related_entity_type' => $data['related_entity_type'] ?? $document->related_entity_type,
                'related_entity_id' => $data['related_entity_id'] ?? $document->related_entity_id,
                'document_date' => $data['document_date'] ?? $document->document_date,
                'due_date' => $data['due_date'] ?? $document->due_date,
                'notes' => $data['notes'] ?? $document->notes,
                'reference' => $data['reference'] ?? $document->reference,
            ]);

            // Supprimer les anciennes lignes
            $document->lines()->delete();

            // Créer les nouvelles lignes
            $lineNumber = 1;
            $subtotal = 0;
            
            foreach ($data['lines'] as $lineData) {
                $line = DocumentLine::create([
                    'document_header_id' => $document->id,
                    'product_id' => $lineData['product_id'],
                    'line_number' => $lineNumber++,
                    'quantity' => $lineData['quantity'],
                    'unit_price' => $lineData['unit_price'],
                    'unit_cost' => $lineData['unit_cost'] ?? null,
                    'discount_percent' => $lineData['discount_percent'] ?? 0,
                    'tax_percent' => $lineData['tax_percent'] ?? 0,
                    'description' => $lineData['description'] ?? null,
                    'notes' => $lineData['notes'] ?? null,
                ]);

                $subtotal += $line->line_total;
            }

            // Mettre à jour les totaux
            $document->subtotal = $subtotal;
            $document->total_amount = $subtotal;
            $document->save();

            return $document->fresh()->load('lines');
        });
    }
}

