<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\NumberingSystem;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\DocumentHeader;
use App\Models\DocumentLine;
use App\Models\User;

class JournalStock extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'journal_stock';

    // Types de mouvement
    public const TYPE_SALE_INVOICE = 'sale_invoice';
    public const TYPE_SALE_DELIVERY = 'sale_delivery';
    public const TYPE_SALE_RETURN = 'sale_return';
    public const TYPE_PURCHASE_INVOICE = 'purchase_invoice';
    public const TYPE_PURCHASE_RECEIPT = 'purchase_receipt';
    public const TYPE_PURCHASE_RETURN = 'purchase_return';
    public const TYPE_TRANSFER_IN = 'transfer_in';
    public const TYPE_TRANSFER_OUT = 'transfer_out';
    public const TYPE_ADJUSTMENT_INCREASE = 'adjustment_increase';
    public const TYPE_ADJUSTMENT_DECREASE = 'adjustment_decrease';
    public const TYPE_MANUAL_ENTRY = 'manual_entry';
    public const TYPE_MANUAL_EXIT = 'manual_exit';

    protected $fillable = [
        'code',
        'movement_type',
        'document_header_id',
        'document_line_id',
        'warehouse_id',
        'product_id',
        'quantity',
        'unit_cost',
        'total_cost',
        'movement_date',
        'reference',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'movement_date' => 'date',
    ];

    protected $guarded = ['code'];

    // Relations
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function documentHeader(): BelongsTo
    {
        return $this->belongsTo(DocumentHeader::class);
    }

    public function documentLine(): BelongsTo
    {
        return $this->belongsTo(DocumentLine::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stockBalance(): HasOne
    {
        return $this->hasOne(StockBalance::class, 'last_movement_id');
    }

    // Scopes
    public function scopeByWarehouse($query, int $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByDateRange($query, $dateFrom, $dateTo)
    {
        return $query->whereBetween('movement_date', [$dateFrom, $dateTo]);
    }

    public function scopeEntries($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeExits($query)
    {
        return $query->where('quantity', '<', 0);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($journal) {
            if (!$journal->code) {
                $journal->code = static::generateCode();
            }
            if (!$journal->created_by && auth()->check()) {
                $journal->created_by = auth()->id();
            }
        });

        static::created(function ($journal) {
            // Mettre à jour le stock balance
            app(\App\Services\JournalStockService::class)
                ->updateStockBalance($journal->warehouse_id, $journal->product_id);
        });
    }

    public static function generateCode(): string
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
