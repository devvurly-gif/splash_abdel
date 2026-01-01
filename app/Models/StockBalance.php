<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'product_id',
        'quantity',
        'reserved_quantity',
        'last_movement_id',
        'last_movement_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'reserved_quantity' => 'decimal:3',
        'last_movement_date' => 'date',
    ];

    // Relations
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function lastMovement(): BelongsTo
    {
        return $this->belongsTo(JournalStock::class, 'last_movement_id');
    }

    // Méthodes
    public function updateFromMovements(): void
    {
        $movements = JournalStock::where('warehouse_id', $this->warehouse_id)
            ->where('product_id', $this->product_id)
            ->get();

        $this->quantity = $movements->sum('quantity');
        
        $lastMovement = $movements->sortByDesc('movement_date')->first();
        if ($lastMovement) {
            $this->last_movement_id = $lastMovement->id;
            $this->last_movement_date = $lastMovement->movement_date;
        }
        
        $this->save();
    }

    public function getAvailableQuantityAttribute(): float
    {
        return $this->quantity - $this->reserved_quantity;
    }
}
