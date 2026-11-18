<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Store;
use App\Models\User;
use Database\Seeders\EventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
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

    // --- Evenementenpagina /events en homepage ---

    /**
     * Vaste "vandaag": woensdag 18 november 2026, 12:00 Nederlandse tijd.
     * Zo blijven de datumafhankelijke tests ook morgen en volgend jaar slagen.
     */
    private function freezeToday(string $moment = '2026-11-18 12:00:00'): void
    {
        $this->travelTo(Carbon::parse($moment, 'Europe/Amsterdam'));
    }

    /** Maakt een evenement direct in de database (lege velden als null, zoals via het formulier). */
    private function makeEvent(string $title, string $date, array $overrides = []): Event
    {
        return Event::create(array_merge([
            'title' => $title,
            'date' => $date,
            'start_time' => null,
            'end_time' => null,
            'location' => null,
            'description' => null,
            'is_visible' => true,
        ], $overrides));
    }

    public function test_events_page_shows_visible_events_from_today_onwards(): void
    {
        $this->freezeToday();
        $this->makeEvent('Gisteren geweest', '2026-11-17');
        $this->makeEvent('Vanochtend al afgelopen', '2026-11-18', ['start_time' => '08:00', 'end_time' => '09:00']);
        $this->makeEvent('Morgen gezellig', '2026-11-19');
        $this->makeEvent('Verborgen morgen', '2026-11-19', ['is_visible' => false]);
        $this->makeEvent('Volgend jaar', '2027-03-01');

        $this->get('/events')
            ->assertOk()
            // Een evenement van vandaag blijft de hele dag staan, ook als het al voorbij is.
            ->assertSeeInOrder(['Vanochtend al afgelopen', 'Morgen gezellig', 'Volgend jaar'])
            ->assertSee('3 evenementen gevonden')
            ->assertDontSee('Gisteren geweest')
            ->assertDontSee('Verborgen morgen');
    }

    public function test_today_is_determined_in_amsterdam_time(): void
    {
        // 23:30 UTC op 18 november is in Nederland al 00:30 op 19 november.
        $this->travelTo(Carbon::parse('2026-11-18 23:30:00', 'UTC'));
        $this->makeEvent('Op de achttiende', '2026-11-18');
        $this->makeEvent('Op de negentiende', '2026-11-19');

        $this->get('/events')->assertSee('Op de negentiende')->assertDontSee('Op de achttiende');
        $this->get('/')->assertSee('Op de negentiende')->assertDontSee('Op de achttiende');
    }

    public function test_events_page_is_sorted_like_the_agenda(): void
    {
        $this->freezeToday();
        $this->makeEvent('D middag', '2026-11-20', ['start_time' => '14:00']);
        $this->makeEvent('C ochtend', '2026-11-20', ['start_time' => '10:00']);
        $this->makeEvent('B zonder tijd', '2026-11-20');
        $this->makeEvent('A eerder', '2026-11-19', ['start_time' => '20:00']);
        $this->makeEvent('E ook om twee', '2026-11-20', ['start_time' => '14:00']);

        $order = ['A eerder', 'B zonder tijd', 'C ochtend', 'D middag', 'E ook om twee'];
        $this->get('/events')->assertSeeInOrder($order);
        $this->assertSame($order, array_column($this->agendaEvents(), 'title'));
    }

    public function test_event_card_shows_real_data_and_skips_empty_fields(): void
    {
        $this->freezeToday();
        $this->makeEvent('Koopavond', '2026-11-20', [
            'start_time' => '18:00', 'end_time' => '21:00',
            'location' => 'Hele winkelcentrum', 'description' => 'Veel winkels zijn langer open.',
        ]);
        $this->makeEvent('Alleen een titel', '2026-12-01');

        $response = $this->get('/events')->assertOk();

        $response->assertSee('Koopavond')
            ->assertSee('vrijdag 20 november 2026')
            ->assertSee('18:00 – 21:00')
            ->assertSee('Hele winkelcentrum')
            ->assertSee('Veel winkels zijn langer open.')
            ->assertSee('<span>NOV</span>20', false)
            ->assertSee('<span>DEC</span>1', false)
            ->assertSee('dinsdag 1 december 2026');

        // Het kale evenement heeft alleen de datumregel: geen lege tijd, locatie of omschrijving.
        $html = $response->getContent();
        preg_match('#<h3>Alleen een titel</h3>(.*?)</article>#s', $html, $card);
        $this->assertStringContainsString('dinsdag 1 december 2026', $card[1]);
        $this->assertStringNotContainsString('◷', $card[1]);
        $this->assertStringNotContainsString('⌾', $card[1]);
        $this->assertStringNotContainsString('<p>', $card[1]);
    }

    public function test_old_sample_data_gratis_label_and_category_buttons_are_gone(): void
    {
        $this->freezeToday();
        $this->makeEvent('Echt evenement', '2026-11-20');

        foreach (['/events', '/'] as $url) {
            $this->get($url)
                ->assertDontSee('GRATIS')
                ->assertDontSee('Lente Fashion Show')
                ->assertDontSee('Food Festival')
                ->assertDontSee('MAART')
                ->assertDontSee('6 evenementen gevonden');
        }

        $this->get('/events')
            ->assertDontSee('Filter evenementen')
            ->assertDontSee('href="#mode"', false);
    }

    public function test_events_page_shows_empty_state(): void
    {
        $this->freezeToday();
        // Alleen een oud en een verborgen evenement: niets geschikt.
        $this->makeEvent('Oud evenement', '2026-01-10');
        $this->makeEvent('Verborgen evenement', '2026-12-10', ['is_visible' => false]);

        $this->get('/events')
            ->assertOk()
            ->assertSee('Nog geen aankomende evenementen')
            ->assertSee('href="'.route('agenda').'"', false)
            ->assertDontSee('Oud evenement')
            ->assertDontSee('Verborgen evenement')
            ->assertDontSee('class="events-grid"', false);
    }

    public function test_events_page_search_works_case_insensitive(): void
    {
        $this->freezeToday();
        $this->makeEvent('Kinderknutselmiddag', '2026-11-21');
        $this->makeEvent('Knutselen voor volwassenen', '2026-11-28');
        $this->makeEvent('Koopavond', '2026-11-20');
        $this->makeEvent('Oude knutselochtend', '2026-11-01');
        $this->makeEvent('Verborgen knutselfeest', '2026-11-25', ['is_visible' => false]);

        $this->get('/events?zoek=KNUTSEL')
            ->assertOk()
            ->assertSee('value="KNUTSEL"', false)
            ->assertSee('2 evenementen gevonden')
            ->assertSee('voor &ldquo;KNUTSEL&rdquo;', false)
            ->assertSeeInOrder(['Kinderknutselmiddag', 'Knutselen voor volwassenen'])
            ->assertDontSee('Koopavond')
            ->assertDontSee('Oude knutselochtend')
            ->assertDontSee('Verborgen knutselfeest')
            ->assertSee('Zoekopdracht wissen');

        $this->get('/events?zoek=koopavond')->assertSee('1 evenement gevonden')->assertSee('Koopavond');
    }

    public function test_events_page_search_without_results_and_empty_search(): void
    {
        $this->freezeToday();
        $this->makeEvent('Koopavond', '2026-11-20');
        $this->makeEvent('Markt', '2026-11-22');

        $this->get('/events?zoek=xyz')
            ->assertOk()
            ->assertSee('Geen evenementen gevonden')
            ->assertSee('Er is geen aankomend evenement met &ldquo;xyz&rdquo; in de titel.', false)
            ->assertSee('Toon alle evenementen')
            ->assertSee('href="'.route('events').'"', false)
            ->assertDontSee('Koopavond');

        // Lege zoekterm (of alleen spaties) = alles tonen, zonder "wissen"-link.
        foreach (['/events?zoek=', '/events?zoek=%20%20', '/events'] as $url) {
            $this->get($url)->assertOk()->assertSee('2 evenementen gevonden')->assertDontSee('Zoekopdracht wissen');
        }

        // Een onverwachte zoekparameter geeft geen serverfout.
        $this->get('/events?zoek[]=test')->assertOk()->assertSee('2 evenementen gevonden');
        $this->get('/events?zoek[a][b]=test')->assertOk();
    }

    public function test_text_is_escaped_on_events_page_and_homepage(): void
    {
        $this->freezeToday();
        $this->makeEvent('<script>alert("x")</script>', '2026-11-20', [
            'location' => '<b>Plein</b>', 'description' => '<img src=x onerror=alert(1)>',
        ]);

        foreach (['/events', '/'] as $url) {
            $this->get($url)
                ->assertDontSee('<script>alert("x")</script>', false)
                ->assertDontSee('<b>Plein</b>', false)
                ->assertSee('&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;', false)
                ->assertSee('&lt;b&gt;Plein&lt;/b&gt;', false);
        }

        $this->get('/events')->assertDontSee('<img src=x', false)->assertSee('&lt;img src=x onerror=alert(1)&gt;', false);

        // De zoekterm zelf wordt ook veilig getoond.
        $this->get('/events?zoek='.urlencode('<script>'))
            ->assertDontSee('value="<script>"', false)
            ->assertSee('value="&lt;script&gt;"', false);
    }

    public function test_homepage_shows_at_most_three_upcoming_events_in_order(): void
    {
        $this->freezeToday();
        $this->makeEvent('Vierde', '2026-12-20');
        $this->makeEvent('Tweede', '2026-11-25', ['start_time' => '10:00', 'location' => 'Centrale plaza']);
        $this->makeEvent('Derde', '2026-11-25', ['start_time' => '15:00', 'end_time' => '16:00']);
        $this->makeEvent('Eerste', '2026-11-18');
        $this->makeEvent('Gisteren', '2026-11-17');
        $this->makeEvent('Verborgen', '2026-11-19', ['is_visible' => false]);

        foreach (['/', '/home'] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('id="events-title"', false)
                ->assertSee('events-grid--3', false)
                ->assertSeeInOrder(['Eerste', 'Tweede', 'Derde'])
                ->assertSee('woensdag 25 november 2026')
                ->assertSee('Centrale plaza')
                ->assertSee('15:00 – 16:00')
                ->assertDontSee('Vierde')
                ->assertDontSee('Gisteren')
                ->assertDontSee('>Verborgen<', false)
                ->assertSee('href="'.route('events').'"', false)
                // Geen links naar detailpagina's die niet bestaan.
                ->assertDontSee('href="'.url('/events').'/', false)
                ->assertDontSee('href="/events/', false);
        }

        $this->assertSame(
            $this->get('/')->getContent(),
            $this->get('/home')->getContent(),
            '/ en /home tonen precies dezelfde pagina.'
        );
    }

    public function test_homepage_handles_one_two_and_zero_events(): void
    {
        $this->freezeToday();
        // De uitgelichte winkels staan los van de evenementen en blijven gewoon staan.
        Store::create(['name' => 'Café Central', 'category' => 'eten', 'is_visible' => true, 'is_featured' => true]);

        $this->get('/')->assertDontSee('id="events-title"', false)
            ->assertDontSee('class="events-grid', false)
            ->assertSee('Bekijk Alle Evenementen')
            ->assertSee('href="'.route('events').'"', false)
            ->assertSee('Uitgelichte Winkels')
            ->assertSee('Café Central');

        $this->makeEvent('Eén evenement', '2026-11-20');
        $this->get('/')->assertSee('id="events-title"', false)->assertSee('events-grid--1', false)->assertSee('Eén evenement');

        $this->makeEvent('Twee evenementen', '2026-11-21');
        $this->get('/home')->assertSee('events-grid--2', false)->assertSeeInOrder(['Eén evenement', 'Twee evenementen']);

        // Alleen oude evenementen telt als "geen geschikte evenementen".
        Event::query()->delete();
        $this->makeEvent('Oud', '2026-11-01');
        $this->get('/')->assertDontSee('id="events-title"', false)->assertDontSee('>Oud<', false)->assertSee('Bekijk Alle Evenementen');
    }

    public function test_cms_changes_show_up_on_events_page_homepage_and_agenda(): void
    {
        $this->freezeToday();
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/evenementen', $this->validData(['title' => 'Proeverij', 'date' => '2026-11-20']));
        $event = Event::sole();
        $this->get('/events')->assertSee('Proeverij');
        $this->get('/')->assertSee('Proeverij');

        // Aanpassen
        $this->actingAs($admin)->put("/admin/evenementen/{$event->id}", $this->validData(['title' => 'Grote proeverij', 'date' => '2026-12-04', 'location' => 'Café Central']));
        $this->get('/events')->assertSee('Grote proeverij')->assertSee('vrijdag 4 december 2026')->assertSee('Café Central');
        $this->get('/')->assertSee('Grote proeverij')->assertSee('vrijdag 4 december 2026');
        $this->assertSame(['Grote proeverij'], array_column($this->agendaEvents(), 'title'));

        // Verbergen: weg op alle drie de openbare plekken, wel nog in het beheer.
        $this->actingAs($admin)->put("/admin/evenementen/{$event->id}", $this->validData(['title' => 'Grote proeverij', 'date' => '2026-12-04', 'is_visible' => '0']));
        $this->get('/events')->assertDontSee('Grote proeverij')->assertSee('Nog geen aankomende evenementen');
        $this->get('/')->assertDontSee('Grote proeverij');
        $this->assertSame([], $this->agendaEvents());
        $this->actingAs($admin)->get('/admin/evenementen')->assertSee('Grote proeverij');

        // Weer zichtbaar en daarna verwijderen
        $this->actingAs($admin)->put("/admin/evenementen/{$event->id}", $this->validData(['title' => 'Grote proeverij', 'date' => '2026-12-04', 'is_visible' => '1']));
        $this->get('/events')->assertSee('Grote proeverij');
        $this->actingAs($admin)->delete("/admin/evenementen/{$event->id}");
        $this->get('/events')->assertDontSee('Grote proeverij');
        $this->get('/')->assertDontSee('Grote proeverij');
        $this->assertSame([], $this->agendaEvents());
    }

    public function test_old_events_stay_available_in_the_agenda_only(): void
    {
        $this->freezeToday();
        $this->makeEvent('Opening 2019', '2019-03-02');
        $this->makeEvent('Vorige week', '2026-11-11');
        $this->makeEvent('Volgende week', '2026-11-25');

        $this->assertSame(['Opening 2019', 'Vorige week', 'Volgende week'], array_column($this->agendaEvents(), 'title'));
        $this->get('/events')->assertSee('Volgende week')->assertDontSee('Vorige week')->assertDontSee('Opening 2019');
        $this->get('/')->assertSee('Volgende week')->assertDontSee('Vorige week')->assertDontSee('Opening 2019');
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
