<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'opening_hours',
        'is_visible',
        'is_featured',
        // 'image_path' staat hier bewust niet: die zet alleen de controller na een geldige upload.
    ];

    /**
     * Map op de 'public'-disk (storage/app/public/stores) waar winkelafbeeldingen staan.
     */
    public const IMAGE_FOLDER = 'stores';

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
     * Webadres van de afbeelding, of null als er geen afbeelding is
     * of als het bestand ontbreekt (dan toont de pagina de fallback).
     */
    public function imageUrl(): ?string
    {
        if (! $this->image_path || ! Storage::disk('public')->exists($this->image_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->image_path);
    }

    /**
     * Leesbare naam van de categorie, bijvoorbeeld "Eten & drinken".
     */
    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
