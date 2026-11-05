<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;

/**
 * Voorbeeldwinkels voor de demonstratie.
 * Uitvoeren met: php artisan db:seed --class=StoreSeeder
 * Veilig om vaker uit te voeren: bestaande winkels (op naam) worden niet gedupliceerd of overschreven,
 * dus ook een zelf ingestelde zichtbaarheid of uitlichting blijft staan.
 */
class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $stores = [
            ['name' => 'Albert Heijn', 'category' => 'supermarkten', 'description' => 'Supermarkt voor de dagelijkse boodschappen, met een ruim assortiment vers.', 'location' => 'Begane grond, bij de hoofdingang', 'website' => 'https://www.ah.nl', 'is_featured' => true],
            ['name' => 'Jumbo', 'category' => 'supermarkten', 'description' => 'Supermarkt met een groot assortiment en een eigen versafdeling.', 'location' => 'Begane grond, aan de parkeerzijde', 'website' => 'https://www.jumbo.com'],
            ['name' => 'Kruidvat', 'category' => 'supermarkten', 'description' => 'Drogisterij voor verzorging, gezondheid en huishoudelijke artikelen.', 'location' => 'Begane grond'],
            ['name' => 'Shoeby', 'category' => 'mode', 'description' => 'Betaalbare mode voor dames, heren en kinderen.', 'location' => 'Winkelpromenade'],
            ['name' => 'Fashion District', 'category' => 'mode', 'description' => 'Trendy kleding voor jong en oud.', 'location' => 'Winkelpromenade', 'is_featured' => true],
            ['name' => 'Café Central', 'category' => 'eten', 'description' => 'De beste koffie in de buurt, met huisgemaakt gebak.', 'location' => 'Centrale plaza', 'is_featured' => true],
            ['name' => 'Bakker Bart', 'category' => 'eten', 'description' => 'Elke dag vers brood, broodjes en zoetigheid.', 'location' => 'Centrale plaza'],
            ['name' => 'PostNL Punt', 'category' => 'diensten', 'description' => 'Pakketten versturen en ophalen.', 'location' => 'In de winkelpassage'],
        ];

        foreach ($stores as $store) {
            Store::firstOrCreate(['name' => $store['name']], $store);
        }
    }
}
