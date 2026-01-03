<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Warehouse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'isprincipal',
        'inchargeOf',
        'type',
    ];

    /**
     * The attributes that are not mass assignable.
     * Code is always auto-generated.
     *
     * @var array<int, string>
     */
    protected $guarded = ['code'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'isprincipal' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($warehouse) {
            // Always auto-generate code
            $warehouse->code = static::generateCode();
        });
    }

    /**
     * Generate code from numbering system.
     *
     * @return string
     */
    public static function generateCode(): string
    {
        $numberingSystem = NumberingSystem::byDomainAndType(
            NumberingSystem::DOMAIN_STRUCTURE,
            'warehouse'
        )->active()->first();

        if (!$numberingSystem) {
            // Fallback: generate a simple code
            return 'WH-' . str_pad((Warehouse::count() + 1), 2, '0', STR_PAD_LEFT);
        }

        return $numberingSystem->getNextNumber();
    }

    /**
     * Get the user in charge of the warehouse.
     */
    public function incharge(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inchargeOf');
    }

    /**
     * Get the products in this warehouse.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_warehouse')
                    ->withPivot('quantity', 'cmup')
                    ->withTimestamps();
    }

    /**
     * Scope to get principal warehouses.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrincipal($query)
    {
        return $query->where('isprincipal', true);
    }

    /**
     * Scope to get warehouses by type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}

