<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    /**
     * Vaste lijst met categorieën: sleutel (opgeslagen in de database) => label (op de website).
     * Een aparte categorietabel is vervolgwerk.
     */
    public const CATEGORIES = [
        'mode' => 'Mode & accessoires',
        'supermarkten' => 'Supermarkten & drogisterij',
        'eten' => 'Eten & drinken',
        'wonen' => 'Wonen & lifestyle',
        'diensten' => 'Diensten',
    ];

    protected $fillable = [
        'name',
        'category',
        'description',
        'location',
        'website',
        'phone',
        'is_visible',
        'is_featured',
    ];

    /**
     * Standaardwaarden voor een nieuwe winkel (bijvoorbeeld in het lege formulier).
     */
    protected $attributes = [
        'is_visible' => true,
        'is_featured' => false,
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Alleen winkels die op de website getoond mogen worden.
     * Gebruik: Store::visible()->get()
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Leesbare naam van de categorie, bijvoorbeeld "Eten & drinken".
     */
    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
