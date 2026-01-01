<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\NumberingSystem;

class Partner extends Model
{
    use HasFactory;

    // Types de tiers
    const TYPE_CUSTOMER = 'customer';
    const TYPE_SUPPLIER = 'supplier';
    const TYPE_BOTH = 'both';

    protected $fillable = [
        'type',
        'name',
        'legal_name',
        'tax_id',
        'registration_number',
        'email',
        'phone',
        'mobile',
        'website',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'payment_terms',
        'credit_limit',
        'discount_percent',
        'status',
        'notes',
    ];

    protected $guarded = ['code'];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'status' => 'boolean',
    ];

    // Relations
    public function documents(): HasMany
    {
        return $this->hasMany(DocumentHeader::class, 'partner_id');
    }

    public function saleDocuments(): HasMany
    {
        return $this->hasMany(DocumentHeader::class, 'partner_id')
            ->where('domain', 'sale');
    }

    public function purchaseDocuments(): HasMany
    {
        return $this->hasMany(DocumentHeader::class, 'partner_id')
            ->where('domain', 'purchase');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    public function scopeCustomers($query)
    {
        return $query->whereIn('type', [self::TYPE_CUSTOMER, self::TYPE_BOTH]);
    }

    public function scopeSuppliers($query)
    {
        return $query->whereIn('type', [self::TYPE_SUPPLIER, self::TYPE_BOTH]);
    }

    public function scopeByType($query, string $type)
    {
        if ($type === 'customer') {
            return $query->customers();
        } elseif ($type === 'supplier') {
            return $query->suppliers();
        }
        return $query->where('type', $type);
    }

    // Boot method pour auto-générer le code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($partner) {
            if (!$partner->code) {
                $partner->code = static::generateCode();
            }
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
            'partner'
        )->active()->first();

        if (!$numberingSystem) {
            // Fallback: generate a simple code
            return 'PTN-' . str_pad((Partner::count() + 1), 4, '0', STR_PAD_LEFT);
        }

        return $numberingSystem->getNextNumber();
    }

    // Accessors
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    public function isCustomer(): bool
    {
        return in_array($this->type, [self::TYPE_CUSTOMER, self::TYPE_BOTH]);
    }

    public function isSupplier(): bool
    {
        return in_array($this->type, [self::TYPE_SUPPLIER, self::TYPE_BOTH]);
    }
}
