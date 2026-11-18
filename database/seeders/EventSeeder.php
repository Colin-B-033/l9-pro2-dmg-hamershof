<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Voorbeeldevenementen voor de demonstratie, rond de huidige maand.
 * Uitvoeren met: php artisan db:seed --class=EventSeeder
 * Veilig om vaker uit te voeren: een evenement met dezelfde titel wordt niet
 * opnieuw aangemaakt en niet overschreven (ook niet als je het hebt aangepast).
 */
class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Eerste dag van de huidige maand, volgens Nederlandse tijd.
        $month = Carbon::now('Europe/Amsterdam')->startOfMonth();

        $events = [
            [
                'title' => 'Modeshow Fashion District',
                'date' => $month->copy()->subMonth()->addDays(20),
                'start_time' => '15:00',
                'end_time' => '16:30',
                'location' => 'Winkelpromenade',
                'description' => 'De nieuwste collectie op de catwalk, gepresenteerd door Fashion District.',
            ],
            [
                'title' => 'Kinderknutselmiddag',
                'date' => $month->copy()->addDays(6),
                'start_time' => '13:00',
                'end_time' => '16:00',
                'location' => 'Centrale plaza',
                'description' => 'Gratis knutselen voor kinderen van 4 tot 10 jaar. Aanmelden is niet nodig.',
            ],
            [
                'title' => 'Koffieproeverij Café Central',
                'date' => $month->copy()->addDays(13),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'location' => 'Café Central',
                'description' => 'Proef drie soorten koffie en hoor alles over de bonen.',
            ],
            // Twee evenementen op dezelfde dag, om te laten zien dat de agenda dat aankan.
            [
                'title' => 'Live muziek op de plaza',
                'date' => $month->copy()->addDays(13),
                'start_time' => '14:00',
                'end_time' => '17:00',
                'location' => 'Centrale plaza',
                'description' => 'Akoestische muziek van lokale artiesten.',
            ],
            [
                'title' => 'Koopavond',
                'date' => $month->copy()->addDays(19),
                'start_time' => '18:00',
                'end_time' => '21:00',
                'location' => 'Hele winkelcentrum',
                'description' => 'Veel winkels zijn deze avond langer open.',
            ],
            [
                'title' => 'Markt op het buitenplein',
                'date' => $month->copy()->addMonth()->addDays(11),
                'start_time' => '11:00',
                'end_time' => '17:00',
                'location' => 'Buitenplein',
                'description' => 'Kraampjes met lokale lekkernijen, cadeaus en ambachtelijke producten.',
            ],
        ];

        foreach ($events as $event) {
            $event['date'] = $event['date']->format('Y-m-d');
            Event::firstOrCreate(['title' => $event['title']], $event);
        }
    }
}
