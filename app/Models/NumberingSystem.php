<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumberingSystem extends Model
{
    use HasFactory;

    /**
     * Available domains for numbering systems.
     */
    public const DOMAIN_STRUCTURE = 'structure';
    public const DOMAIN_SALE = 'sale';
    public const DOMAIN_PURCHASE = 'purchase';
    public const DOMAIN_STOCK = 'stock';

    /**
     * Get all available domains.
     *
     * @return array
     */
    public static function getAvailableDomains(): array
    {
        return [
            self::DOMAIN_STRUCTURE,
            self::DOMAIN_SALE,
            self::DOMAIN_PURCHASE,
            self::DOMAIN_STOCK,
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'domain',
        'type',
        'template',
        'next_trick',
        'isActive',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'next_trick' => 'integer',
        'isActive' => 'boolean',
    ];

    /**
     * Generate the next number based on the template and next_trick value.
     *
     * @return string
     */
    public function generateNextNumber(): string
    {
        $generated = $this->template;
        $now = now();

        // Replace date placeholders
        $generated = str_replace('{YYYY}', $now->format('Y'), $generated);
        $generated = str_replace('{YY}', $now->format('y'), $generated);
        $generated = str_replace('{MM}', $now->format('m'), $generated);
        $generated = str_replace('{DD}', $now->format('d'), $generated);
        $generated = str_replace('{D}', $now->format('j'), $generated); // Day without leading zero

        // Handle zero padding patterns like {0000} or {00000}
        if (preg_match('/\{0+\}/', $generated, $matches)) {
            $zeroPattern = $matches[0];
            $padding = strlen($zeroPattern) - 2; // Subtract 2 for { and }
            $number = str_pad($this->next_trick, $padding, '0', STR_PAD_LEFT);
            $generated = str_replace($zeroPattern, $number, $generated);
        }

        // Handle {NUMBER:5} format (backward compatibility)
        if (preg_match('/\{NUMBER:(\d+)\}/', $generated, $matches)) {
            $padding = (int) $matches[1];
            $number = str_pad($this->next_trick, $padding, '0', STR_PAD_LEFT);
            $generated = str_replace('{NUMBER:' . $padding . '}', $number, $generated);
        }

        // Handle simple {NUMBER} placeholder (backward compatibility)
        if (preg_match('/\{NUMBER\}/', $generated)) {
            $padding = $this->calculatePadding();
            $number = str_pad($this->next_trick, $padding, '0', STR_PAD_LEFT);
            $generated = str_replace('{NUMBER}', $number, $generated);
        }

        return $generated;
    }

    /**
     * Calculate padding based on zeros in template.
     *
     * @return int
     */
    protected function calculatePadding(): int
    {
        // Count zeros in template (e.g., "PDT-00001" has 5 zeros)
        if (preg_match('/0+/', $this->template, $matches)) {
            return strlen($matches[0]);
        }
        
        // Default to 3 if no pattern found
        return 3;
    }

    /**
     * Get the next number and increment next_trick.
     *
     * @return string
     */
    public function getNextNumber(): string
    {
        $number = $this->generateNextNumber();
        $this->increment('next_trick');
        return $number;
    }

    /**
     * Scope to get active numbering systems.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('isActive', true);
    }

    /**
     * Scope to get numbering systems by domain and type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $domain
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDomainAndType($query, string $domain, string $type)
    {
        return $query->where('domain', $domain)->where('type', $type);
    }
}
