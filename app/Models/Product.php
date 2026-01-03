<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\NumberingSystem;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ean13',
        'category_id',
        'brand_id',
        'purchase_price',
        'sale_price',
        'description',
        'tax_id',
        'unit',
        'isactive',
        'onPromo',
        'isFeatured',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'isactive' => 'boolean',
        'onPromo' => 'boolean',
        'isFeatured' => 'boolean',
    ];

    protected $guarded = ['code'];

    // Relations
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function documentLines(): HasMany
    {
        return $this->hasMany(DocumentLine::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalStock::class);
    }

    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->ordered();
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('isprimary', true);
    }

    /**
     * Get the warehouses that contain this product.
     */
    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'product_warehouse')
                    ->withPivot('quantity', 'cmup')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('isactive', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('isactive', false);
    }

    // Boot method pour auto-générer le code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (!$product->code) {
                $product->code = static::generateCode();
            }
        });
    }

    public static function generateCode(): string
    {
        $numberingSystem = NumberingSystem::byDomainAndType(
            NumberingSystem::DOMAIN_STRUCTURE,
            'product'
        )->active()->first();

        if (!$numberingSystem) {
            return 'PDT-' . str_pad((Product::count() + 1), 5, '0', STR_PAD_LEFT);
        }

        return $numberingSystem->getNextNumber();
    }
}
