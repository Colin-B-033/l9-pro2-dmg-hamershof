<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

/**
 * Evenementenbeheer in het CMS (/admin/evenementen).
 * De routes zijn beveiligd met de middleware 'auth' en 'admin' (zie routes/web.php).
 * Zichtbare evenementen verschijnen op de openbare agenda (/agenda).
 */
class EventController extends Controller
{
    public function index()
    {
        // Ook verborgen evenementen, zodat de beheerder ze kan terugvinden en bewerken.
        $events = Event::query()->inAgendaOrder()->get();

        return view('admin.events.index', ['events' => $events]);
    }

    public function create()
    {
        return view('admin.events.form', ['event' => new Event]);
    }

    public function store(Request $request)
    {
        $event = Event::create($this->validateEvent($request));

        return redirect()->route('admin.events.index')
            ->with('success', "Evenement \"{$event->title}\" is toegevoegd.");
    }

    public function edit(Event $event)
    {
        return view('admin.events.form', ['event' => $event]);
    }

    public function update(Request $request, Event $event)
    {
        $event->update($this->validateEvent($request));

        return redirect()->route('admin.events.index')
            ->with('success', "Evenement \"{$event->title}\" is opgeslagen.");
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', "Evenement \"{$event->title}\" is verwijderd.");
    }

    /**
     * Valideert het evenementformulier. Bij een fout stuurt Laravel de gebruiker
     * automatisch terug met de foutmeldingen en de ingevulde waarden (old()).
     */
    private function validateEvent(Request $request): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:100'],
            // date_format controleert ook of de dag echt bestaat (30 februari wordt geweigerd).
            'date' => ['required', 'date_format:Y-m-d'],
            'start_time' => ['nullable', 'date_format:H:i'],
            // bail: stop bij de eerste fout, zodat je maar één duidelijke melding krijgt.
            // prohibited_if: een eindtijd mag alleen als er ook een begintijd is.
            // after: de eindtijd moet later zijn dan de begintijd (gelijk mag dus niet).
            'end_time' => ['bail', 'nullable', 'date_format:H:i', 'prohibited_if:start_time,null', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_visible' => ['boolean'],
        ];

        $messages = [
            'required' => ':Attribute is verplicht.',
            'max' => ':Attribute mag maximaal :max tekens bevatten.',
            'date.date_format' => 'Vul een geldige datum in.',
            'start_time.date_format' => 'Vul een geldige begintijd in, bijvoorbeeld 14:00.',
            'end_time.date_format' => 'Vul een geldige eindtijd in, bijvoorbeeld 16:00.',
            'end_time.prohibited_if' => 'Vul ook een begintijd in, of laat de eindtijd leeg.',
            'end_time.after' => 'De eindtijd moet later zijn dan de begintijd. Een evenement valt binnen één dag.',
            'boolean' => 'Ongeldige keuze bij :attribute.',
        ];

        $attributes = [
            'title' => 'titel',
            'date' => 'datum',
            'start_time' => 'begintijd',
            'end_time' => 'eindtijd',
            'location' => 'locatie',
            'description' => 'omschrijving',
            'is_visible' => 'zichtbaar in de agenda',
        ];

        $data = $request->validate($rules, $messages, $attributes);

        // Checkbox: het formulier stuurt "0" (verborgen veld) of "1" (aangevinkt).
        // $request->boolean() maakt daar altijd true of false van, dus uitvinken wordt echt false.
        $data['is_visible'] = $request->boolean('is_visible');

        return $data;
    }
}
