<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\NumberingSystem;
use App\Models\Warehouse;
use App\Models\Partner;
use App\Models\User;

class DocumentHeader extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'domain',
        'type',
        'warehouse_id',
        'partner_id',
        'related_entity_type',
        'related_entity_id',
        'document_date',
        'due_date',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'notes',
        'reference',
        'created_by',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'document_date' => 'date',
        'due_date' => 'date',
        'validated_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relations
    public function lines(): HasMany
    {
        return $this->hasMany(DocumentLine::class, 'document_header_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function relatedEntity(): MorphTo
    {
        return $this->morphTo();
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalStock::class, 'document_header_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // Scopes
    public function scopeByDomain($query, string $domain)
    {
        return $query->where('domain', $domain);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeValidated($query)
    {
        return $query->where('status', 'validated');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // Méthodes
    public function validate(): void
    {
        if ($this->status !== 'draft') {
            throw new \Exception('Document déjà validé ou annulé');
        }

        $this->status = 'validated';
        $this->validated_by = auth()->id();
        $this->validated_at = now();
        $this->save();

        // Déclencher la création des mouvements de stock
        $this->createStockMovements();
    }

    protected function createStockMovements(): void
    {
        $service = app(\App\Services\JournalStockService::class);
        
        // Ensure lines are loaded
        if (!$this->relationLoaded('lines')) {
            $this->load('lines');
        }
        
        foreach ($this->lines as $line) {
            $movementType = $this->getMovementType($line);
            if ($movementType) {
                $service->createFromDocument($this, $line, $movementType);
            }
        }
    }

    protected function getMovementType(?DocumentLine $line = null): ?string
    {
        $mapping = [
            'sale' => [
                'invoice' => 'sale_invoice',
                'delivery_note' => 'sale_delivery',
                'return' => 'sale_return',
            ],
            'purchase' => [
                'invoice' => 'purchase_invoice',
                'receipt' => 'purchase_receipt',
                'return' => 'purchase_return',
            ],
            'stock' => [
                'adjustment' => null, // Will be determined by quantity sign
                'manual_entry' => 'manual_entry',
                'manual_exit' => 'manual_exit',
            ],
        ];

        $baseType = $mapping[$this->domain][$this->type] ?? null;
        
        // Special handling for adjustments: determine increase or decrease based on quantity
        if ($this->type === 'adjustment' && $line) {
            return $line->quantity >= 0 ? 'adjustment_increase' : 'adjustment_decrease';
        }
        
        return $baseType;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($header) {
            if (!$header->code) {
                $header->code = static::generateCode($header->domain, $header->type);
            }
        });
    }

    public static function generateCode(string $domain, string $type): string
    {
        $numberingSystem = NumberingSystem::byDomainAndType($domain, $type)
            ->active()
            ->first();

        if (!$numberingSystem) {
            // Fallback
            $prefix = strtoupper(substr($domain, 0, 1) . substr($type, 0, 3));
            return $prefix . '-' . now()->format('Y') . '-' . str_pad(
                DocumentHeader::where('domain', $domain)
                    ->where('type', $type)
                    ->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );
        }

        return $numberingSystem->getNextNumber();
    }
}
