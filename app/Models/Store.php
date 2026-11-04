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
    ];

    /**
     * Leesbare naam van de categorie, bijvoorbeeld "Eten & drinken".
     */
    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
