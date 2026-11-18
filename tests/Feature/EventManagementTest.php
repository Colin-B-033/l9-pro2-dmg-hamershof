<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Database\Seeders\EventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Evenementenbeheer in het CMS (/admin/evenementen) en de openbare agenda (/agenda).
 * Draait op de in-memory testdatabase uit phpunit.xml; de ontwikkeldatabase wordt niet aangeraakt.
 */
class EventManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // De tests hebben geen gebouwde frontend (public/build) nodig.
        $this->withoutVite();
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Kinderknutselmiddag',
            'date' => '2026-11-18',
            'start_time' => '13:00',
            'end_time' => '16:00',
            'location' => 'Centrale plaza',
            'description' => 'Gratis knutselen voor kinderen.',
            // Zoals het formulier hem verstuurt: "1" = aangevinkt, "0" = uitgevinkt (verborgen veld).
            'is_visible' => '1',
        ], $overrides);
    }

    /**
     * Leest de evenementen die /agenda als JSON aan agenda.js meegeeft.
     */
    private function agendaEvents(): array
    {
        $html = $this->get('/agenda')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('#<script id="agenda-events" type="application/json">(.*?)</script>#s', $html);
        preg_match('#<script id="agenda-events" type="application/json">(.*?)</script>#s', $html, $match);

        return json_decode($match[1], true, flags: JSON_THROW_ON_ERROR);
    }

    // --- Toegang ---

    public function test_guest_is_redirected_to_login(): void
    {
        $event = Event::create($this->validData());

        $this->get('/admin/evenementen')->assertRedirect('/login');
        $this->get('/admin/evenementen/create')->assertRedirect('/login');
        $this->get("/admin/evenementen/{$event->id}/edit")->assertRedirect('/login');
        $this->post('/admin/evenementen', $this->validData(['title' => 'Nieuw']))->assertRedirect('/login');
        $this->put("/admin/evenementen/{$event->id}", $this->validData(['title' => 'Gewijzigd']))->assertRedirect('/login');
        $this->delete("/admin/evenementen/{$event->id}")->assertRedirect('/login');

        $this->assertDatabaseCount('events', 1);
        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'Kinderknutselmiddag']);
    }

    public function test_regular_user_has_no_access_to_admin(): void
    {
        $user = User::factory()->create();
        $event = Event::create($this->validData());

        $this->actingAs($user)->get('/admin/evenementen')->assertForbidden();
        $this->actingAs($user)->get('/admin/evenementen/create')->assertForbidden();
        $this->actingAs($user)->get("/admin/evenementen/{$event->id}/edit")->assertForbidden();
        $this->actingAs($user)->post('/admin/evenementen', $this->validData(['title' => 'Nieuw']))->assertForbidden();
        $this->actingAs($user)->put("/admin/evenementen/{$event->id}", $this->validData(['title' => 'Gewijzigd']))->assertForbidden();
        $this->actingAs($user)->delete("/admin/evenementen/{$event->id}")->assertForbidden();

        $this->assertDatabaseCount('events', 1);
        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'Kinderknutselmiddag']);
    }

    public function test_admin_layout_has_agenda_link(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/winkels')
            ->assertOk()
            ->assertSee('href="'.route('admin.events.index').'"', false)
            ->assertSee('>Agenda</a>', false);
    }

    // --- Toevoegen, bewerken, verwijderen ---

    public function test_admin_sees_empty_state_without_events(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/evenementen')
            ->assertOk()
            ->assertSee('Nog geen evenementen')
            ->assertSee('Eerste evenement toevoegen');
    }

    public function test_new_event_form_has_visible_checked(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/evenementen/create')
            ->assertOk()
            ->assertSee('id="is_visible" name="is_visible" value="1" checked', false);
    }

    public function test_admin_can_create_an_event(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/evenementen', $this->validData())
            ->assertRedirect('/admin/evenementen')
            ->assertSessionHas('success', 'Evenement "Kinderknutselmiddag" is toegevoegd.');

        $event = Event::firstOrFail();
        $this->assertSame('2026-11-18', $event->date->format('Y-m-d'));
        $this->assertSame('13:00', $event->start_time);
        $this->assertSame('16:00', $event->end_time);
        $this->assertSame('Centrale plaza', $event->location);
        $this->assertTrue($event->is_visible);

        $this->actingAs($this->admin())
            ->get('/admin/evenementen')
            ->assertSee('Kinderknutselmiddag')
            ->assertSee('18-11-2026')
            ->assertSee('13:00 – 16:00');
    }

    public function test_only_title_and_date_are_required(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/evenementen', [
                'title' => 'Koopavond',
                'date' => '2026-12-04',
                'start_time' => '',
                'end_time' => '',
                'location' => '',
                'description' => '',
                'is_visible' => '1',
            ])
            ->assertSessionHasNoErrors();

        $event = Event::firstOrFail();
        $this->assertNull($event->start_time);
        $this->assertNull($event->end_time);
        $this->assertNull($event->location);
        $this->assertNull($event->timeLabel());
    }

    public function test_admin_can_update_an_event(): void
    {
        $event = Event::create($this->validData());

        $this->actingAs($this->admin())
            ->get("/admin/evenementen/{$event->id}/edit")
            ->assertOk()
            ->assertSee('value="Kinderknutselmiddag"', false)
            ->assertSee('value="2026-11-18"', false)
            ->assertSee('value="13:00"', false);

        $this->actingAs($this->admin())
            ->put("/admin/evenementen/{$event->id}", $this->validData([
                'title' => 'Grote knutselmiddag',
                'date' => '2026-11-25',
                'start_time' => '14:00',
                'end_time' => '',
            ]))
            ->assertRedirect('/admin/evenementen')
            ->assertSessionHas('success', 'Evenement "Grote knutselmiddag" is opgeslagen.');

        $event->refresh();
        $this->assertSame('Grote knutselmiddag', $event->title);
        $this->assertSame('2026-11-25', $event->date->format('Y-m-d'));
        $this->assertSame('14:00', $event->start_time);
        $this->assertNull($event->end_time);
        $this->assertSame(1, Event::count());
    }

    public function test_admin_can_delete_an_event(): void
    {
        $event = Event::create($this->validData());

        $this->actingAs($this->admin())
            ->delete("/admin/evenementen/{$event->id}")
            ->assertRedirect('/admin/evenementen')
            ->assertSessionHas('success', 'Evenement "Kinderknutselmiddag" is verwijderd.');

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
        $this->assertSame([], $this->agendaEvents());
    }

    public function test_delete_form_asks_for_confirmation_and_uses_csrf(): void
    {
        Event::create($this->validData());

        $this->actingAs($this->admin())
            ->get('/admin/evenementen')
            ->assertSee('return confirm(', false)
            ->assertSee('name="_token"', false)
            ->assertSee('name="_method" value="DELETE"', false);
    }

    public function test_admin_overview_is_sorted_by_date_and_start_time(): void
    {
        Event::create($this->validData(['title' => 'Derde', 'date' => '2026-12-01', 'start_time' => '09:00', 'end_time' => '']));
        Event::create($this->validData(['title' => 'Tweede', 'date' => '2026-11-18', 'start_time' => '15:00', 'end_time' => '']));
        Event::create($this->validData(['title' => 'Eerste', 'date' => '2026-11-18', 'start_time' => '10:00', 'end_time' => '']));

        $this->actingAs($this->admin())
            ->get('/admin/evenementen')
            ->assertSeeInOrder(['Eerste', 'Tweede', 'Derde']);
    }

    // --- Validatie ---

    public function test_title_and_date_are_required_with_dutch_messages(): void
    {
        $this->actingAs($this->admin())
            ->from('/admin/evenementen/create')
            ->post('/admin/evenementen', $this->validData(['title' => '', 'date' => '']))
            ->assertRedirect('/admin/evenementen/create')
            ->assertSessionHasErrors([
                'title' => 'Titel is verplicht.',
                'date' => 'Datum is verplicht.',
            ])
            ->assertSessionHasInput('location', 'Centrale plaza');

        $this->assertDatabaseCount('events', 0);
    }

    public function test_invalid_dates_and_too_long_texts_are_rejected(): void
    {
        $admin = $this->admin();

        foreach (['2026-02-30', '2026-13-01', '18-11-2026', 'morgen'] as $date) {
            $this->actingAs($admin)
                ->post('/admin/evenementen', $this->validData(['date' => $date]))
                ->assertSessionHasErrors(['date' => 'Vul een geldige datum in.']);
        }

        $this->actingAs($admin)
            ->post('/admin/evenementen', $this->validData([
                'title' => str_repeat('a', 101),
                'location' => str_repeat('b', 151),
                'description' => str_repeat('c', 1001),
            ]))
            ->assertSessionHasErrors([
                'title' => 'Titel mag maximaal 100 tekens bevatten.',
                'location' => 'Locatie mag maximaal 150 tekens bevatten.',
                'description' => 'Omschrijving mag maximaal 1000 tekens bevatten.',
            ]);

        $this->assertDatabaseCount('events', 0);
    }

    public function test_invalid_time_combinations_are_rejected(): void
    {
        $admin = $this->admin();

        // Eindtijd zonder begintijd
        $this->actingAs($admin)
            ->post('/admin/evenementen', $this->validData(['start_time' => '', 'end_time' => '16:00']))
            ->assertSessionHasErrors(['end_time' => 'Vul ook een begintijd in, of laat de eindtijd leeg.']);

        // Eindtijd vóór of gelijk aan de begintijd (ook "over middernacht" kan niet: één dag)
        foreach ([['14:00', '13:00'], ['14:00', '14:00'], ['23:00', '01:00']] as [$start, $end]) {
            $this->actingAs($admin)
                ->post('/admin/evenementen', $this->validData(['start_time' => $start, 'end_time' => $end]))
                ->assertSessionHasErrors(['end_time' => 'De eindtijd moet later zijn dan de begintijd. Een evenement valt binnen één dag.']);
        }

        // Geen geldige tijd
        $this->actingAs($admin)
            ->post('/admin/evenementen', $this->validData(['start_time' => '25:00', 'end_time' => '']))
            ->assertSessionHasErrors(['start_time' => 'Vul een geldige begintijd in, bijvoorbeeld 14:00.']);

        $this->assertDatabaseCount('events', 0);

        // Alleen een begintijd mag wel
        $this->actingAs($admin)
            ->post('/admin/evenementen', $this->validData(['start_time' => '20:00', 'end_time' => '']))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseCount('events', 1);
    }

    public function test_old_dates_can_be_entered_and_reach_the_agenda(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/evenementen', $this->validData(['title' => 'Opening 2019', 'date' => '2019-03-02']))
            ->assertSessionHasNoErrors();

        $this->assertSame('2019-03-02', $this->agendaEvents()[0]['date']);
    }

    // --- Zichtbaarheid ---

    public function test_unchecking_visible_really_saves_false_and_can_be_turned_back_on(): void
    {
        $event = Event::create($this->validData());

        // Uitgevinkt: het formulier stuurt dan alleen het verborgen veld met "0".
        $this->actingAs($this->admin())
            ->put("/admin/evenementen/{$event->id}", $this->validData(['is_visible' => '0']))
            ->assertRedirect('/admin/evenementen');
        $this->assertFalse($event->fresh()->is_visible);
        $this->assertDatabaseHas('events', ['id' => $event->id, 'is_visible' => false]);

        $this->actingAs($this->admin())
            ->put("/admin/evenementen/{$event->id}", $this->validData(['is_visible' => '1']));
        $this->assertTrue($event->fresh()->is_visible);
    }

    public function test_checkbox_choice_is_kept_after_a_validation_error(): void
    {
        $this->actingAs($this->admin())
            ->from('/admin/evenementen/create')
            ->post('/admin/evenementen', $this->validData(['title' => '', 'is_visible' => '0']))
            ->assertSessionHasErrors('title');

        // Het formulier opnieuw openen (met de oude invoer uit de sessie).
        $this->actingAs($this->admin())
            ->get('/admin/evenementen/create')
            ->assertDontSee('id="is_visible" name="is_visible" value="1" checked', false)
            ->assertSee('value="Centrale plaza"', false)
            ->assertSee('value="2026-11-18"', false)
            ->assertSee('value="13:00"', false);
    }

    public function test_hidden_event_stays_editable_but_is_not_public(): void
    {
        Event::create($this->validData(['title' => 'Zichtbare middag']));
        $hidden = Event::create($this->validData(['title' => 'Geheime proeverij', 'is_visible' => false]));

        $titles = array_column($this->agendaEvents(), 'title');
        $this->assertSame(['Zichtbare middag'], $titles);
        $this->get('/agenda')->assertDontSee('Geheime proeverij');

        $this->actingAs($this->admin())
            ->get('/admin/evenementen')
            ->assertSee('Geheime proeverij')
            ->assertSee('admin-status--hidden">Verborgen', false);

        $this->actingAs($this->admin())
            ->get("/admin/evenementen/{$hidden->id}/edit")
            ->assertOk()
            ->assertSee('value="Geheime proeverij"', false)
            ->assertDontSee('id="is_visible" name="is_visible" value="1" checked', false);
    }

    // --- Openbare agenda ---

    public function test_agenda_receives_visible_events_with_only_the_needed_fields(): void
    {
        Event::create($this->validData(['title' => 'Middag', 'date' => '2026-11-18', 'start_time' => '14:00', 'end_time' => '']));
        Event::create($this->validData(['title' => 'Ochtend', 'date' => '2026-11-18', 'start_time' => '10:00', 'end_time' => '11:30']));
        // Direct met create() gaat lege invoer niet via het formulier, dus hier null in plaats van ''.
        Event::create($this->validData(['title' => 'Volgende maand', 'date' => '2026-12-01', 'start_time' => null, 'end_time' => null, 'location' => null, 'description' => null]));

        $events = $this->agendaEvents();

        // Gesorteerd op datum en begintijd, en twee evenementen op dezelfde dag.
        $this->assertSame(['Ochtend', 'Middag', 'Volgende maand'], array_column($events, 'title'));
        $this->assertSame(
            ['id', 'title', 'date', 'start_time', 'end_time', 'location', 'description'],
            array_keys($events[0])
        );
        $this->assertSame('2026-11-18', $events[0]['date']);
        $this->assertSame('10:00', $events[0]['start_time']);
        $this->assertSame('11:30', $events[0]['end_time']);
        $this->assertSame('Centrale plaza', $events[0]['location']);
        $this->assertNull($events[2]['start_time']);
        $this->assertNull($events[2]['location']);
    }

    public function test_agenda_works_without_events(): void
    {
        $this->assertSame([], $this->agendaEvents());
        $this->get('/agenda')->assertSee('id="calendar-days"', false)->assertSee('data-view="list"', false);
    }

    public function test_changes_in_the_cms_show_up_in_the_agenda(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/evenementen', $this->validData());
        $event = Event::firstOrFail();
        $this->assertSame(['Kinderknutselmiddag'], array_column($this->agendaEvents(), 'title'));

        $this->actingAs($admin)->put("/admin/evenementen/{$event->id}", $this->validData(['title' => 'Knutselfeest', 'date' => '2026-12-05']));
        $this->assertSame([['Knutselfeest', '2026-12-05']], array_map(fn ($e) => [$e['title'], $e['date']], $this->agendaEvents()));

        $this->actingAs($admin)->put("/admin/evenementen/{$event->id}", $this->validData(['title' => 'Knutselfeest', 'is_visible' => '0']));
        $this->assertSame([], $this->agendaEvents());

        $this->actingAs($admin)->put("/admin/evenementen/{$event->id}", $this->validData(['title' => 'Knutselfeest', 'is_visible' => '1']));
        $this->actingAs($admin)->delete("/admin/evenementen/{$event->id}");
        $this->assertSame([], $this->agendaEvents());
    }

    public function test_text_is_passed_and_shown_safely(): void
    {
        $evil = '</script><script>alert("x")</script> & \'quote\'';
        Event::create($this->validData(['title' => $evil, 'location' => '<b>Plein</b>', 'description' => '<img src=x onerror=alert(1)>']));

        $response = $this->get('/agenda')->assertOk();

        // Geen ruwe HTML van de gebruiker in de pagina...
        $response->assertDontSee('<script>alert("x")</script>', false)
            ->assertDontSee('<img src=x onerror', false)
            ->assertDontSee('<b>Plein</b>', false);

        // ...maar na het inlezen van de JSON is de tekst precies wat de beheerder invulde.
        $event = $this->agendaEvents()[0];
        $this->assertSame($evil, $event['title']);
        $this->assertSame('<b>Plein</b>', $event['location']);
        $this->assertSame('<img src=x onerror=alert(1)>', $event['description']);

        // Ook in het beheer wordt de tekst ge-escaped.
        $this->actingAs($this->admin())
            ->get('/admin/evenementen')
            ->assertDontSee('<script>alert("x")</script>', false)
            ->assertSee('&lt;/script&gt;&lt;script&gt;', false);
    }

    public function test_public_agenda_script_uses_text_content_instead_of_inner_html(): void
    {
        $script = file_get_contents(public_path('js/agenda.js'));

        // Zoekt naar echt gebruik (".innerHTML =", "insertAdjacentHTML(" ...), niet naar het woord in commentaar.
        $this->assertStringContainsString('.textContent = ', $script);
        $this->assertDoesNotMatchRegularExpression('/\.(innerHTML|outerHTML)\s*\+?=|insertAdjacentHTML\s*\(|document\.write\s*\(/', $script);
    }

    // --- Voorbeeldgegevens ---

    public function test_event_seeder_does_not_overwrite_or_duplicate_events(): void
    {
        Event::create($this->validData(['title' => 'Koopavond', 'date' => '2030-01-01', 'is_visible' => false]));

        $this->seed(EventSeeder::class);
        $count = Event::count();
        $this->seed(EventSeeder::class);

        $this->assertSame($count, Event::count());
        $koopavond = Event::where('title', 'Koopavond')->sole();
        $this->assertSame('2030-01-01', $koopavond->date->format('Y-m-d'));
        $this->assertFalse($koopavond->is_visible);
        $this->assertDatabaseCount('users', 0);
    }
}
