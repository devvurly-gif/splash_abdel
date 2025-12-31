<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'status',
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
        'status' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            // Always auto-generate code
            $brand->code = static::generateCode();
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
            'brand'
        )->active()->first();

        if (!$numberingSystem) {
            // Fallback: generate a simple code
            return 'BR-' . str_pad((Brand::count() + 1), 3, '0', STR_PAD_LEFT);
        }

        return $numberingSystem->getNextNumber();
    }

    /**
     * Scope to get active brands.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope to get inactive brands.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }
}

