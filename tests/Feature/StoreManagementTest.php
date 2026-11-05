<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Winkelbeheer in het CMS en de openbare pagina /winkels.
 * Draait op de in-memory testdatabase uit phpunit.xml; de ontwikkeldatabase wordt niet aangeraakt.
 */
class StoreManagementTest extends TestCase
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
            'name' => 'Boekhandel Leusden',
            'category' => 'diensten',
            'description' => 'Boeken, tijdschriften en cadeaus.',
            'location' => 'Winkelpromenade',
            'website' => 'https://www.voorbeeld.nl',
            'phone' => '033 123 45 67',
            // Zoals het formulier ze verstuurt: "1" = aangevinkt, "0" = uitgevinkt (verborgen veld).
            'is_visible' => '1',
            'is_featured' => '0',
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/winkels')->assertRedirect('/login');
        $this->get('/admin/winkels?zoek=boek')->assertRedirect('/login');
        $this->post('/admin/winkels', $this->validData())->assertRedirect('/login');

        $this->assertDatabaseCount('stores', 0);
    }

    public function test_regular_user_has_no_access_to_admin(): void
    {
        $user = User::factory()->create();
        $store = Store::create($this->validData());

        $this->actingAs($user)->get('/admin/winkels')->assertForbidden();
        $this->actingAs($user)->get('/admin/winkels?zoek=boek')->assertForbidden();
        $this->actingAs($user)->post('/admin/winkels', $this->validData(['name' => 'Nieuw']))->assertForbidden();
        $this->actingAs($user)->put("/admin/winkels/{$store->id}", $this->validData(['name' => 'Gewijzigd']))->assertForbidden();
        $this->actingAs($user)->delete("/admin/winkels/{$store->id}")->assertForbidden();

        $this->assertDatabaseHas('stores', ['id' => $store->id, 'name' => 'Boekhandel Leusden']);
        $this->assertDatabaseCount('stores', 1);
    }

    public function test_registration_cannot_make_someone_admin(): void
    {
        $this->post('/register', [
            'name' => 'Nieuwe gebruiker',
            'email' => 'nieuw@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'is_admin' => '1',
        ]);

        $this->assertFalse(User::where('email', 'nieuw@example.com')->firstOrFail()->is_admin);
    }

    public function test_admin_can_create_a_store_that_appears_on_the_public_page(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/winkels', $this->validData())
            ->assertRedirect('/admin/winkels')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('stores', ['name' => 'Boekhandel Leusden', 'category' => 'diensten']);

        $this->get('/winkels')
            ->assertOk()
            ->assertSee('Boekhandel Leusden')
            ->assertSee('Boeken, tijdschriften en cadeaus.');
    }

    public function test_admin_can_update_a_store(): void
    {
        $store = Store::create($this->validData());

        $this->actingAs($this->admin())
            ->get("/admin/winkels/{$store->id}/edit")
            ->assertOk()
            ->assertSee('Boekhandel Leusden');

        $this->actingAs($this->admin())
            ->put("/admin/winkels/{$store->id}", $this->validData(['name' => 'Boekhandel De Hamershof', 'category' => 'wonen']))
            ->assertRedirect('/admin/winkels');

        $this->assertDatabaseHas('stores', ['id' => $store->id, 'name' => 'Boekhandel De Hamershof', 'category' => 'wonen']);
        $this->get('/winkels')->assertSee('Boekhandel De Hamershof')->assertDontSee('Boekhandel Leusden');
    }

    public function test_admin_can_delete_a_store(): void
    {
        $store = Store::create($this->validData());

        $this->actingAs($this->admin())
            ->delete("/admin/winkels/{$store->id}")
            ->assertRedirect('/admin/winkels')
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('stores', ['id' => $store->id]);
        $this->get('/winkels')->assertDontSee('Boekhandel Leusden');
    }

    public function test_invalid_input_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->from('/admin/winkels/create')
            ->post('/admin/winkels', $this->validData([
                'name' => '',
                'category' => 'bestaat-niet',
                'website' => 'javascript:alert(1)',
                'phone' => 'bel mij',
            ]))
            ->assertRedirect('/admin/winkels/create')
            ->assertSessionHasErrors(['name', 'category', 'website', 'phone'])
            ->assertSessionHasInput('description', 'Boeken, tijdschriften en cadeaus.');

        $this->assertDatabaseCount('stores', 0);
    }

    public function test_store_text_is_escaped_on_the_public_page(): void
    {
        Store::create($this->validData(['name' => '<script>alert("x")</script>']));

        $this->get('/winkels')
            ->assertOk()
            ->assertDontSee('<script>alert("x")</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_public_page_shows_empty_state_and_filters_by_category(): void
    {
        $this->get('/winkels')->assertOk()->assertSee('nog geen winkels toegevoegd');

        Store::create($this->validData(['name' => 'Albert Heijn', 'category' => 'supermarkten']));
        Store::create($this->validData(['name' => 'Shoeby', 'category' => 'mode']));

        $this->get('/winkels?categorie=mode')->assertSee('Shoeby')->assertDontSee('Albert Heijn');
        $this->get('/winkels?categorie=onbekend')->assertSee('Shoeby')->assertSee('Albert Heijn');
    }

    public function test_homepage_is_shown_on_root_and_home(): void
    {
        $this->get('/')->assertOk()->assertSee('Alles op één plek');
        $this->get('/home')->assertOk()->assertSee('Alles op één plek');
    }

    public function test_admin_is_sent_to_cms_after_login(): void
    {
        $this->actingAs($this->admin())->get('/dashboard')->assertRedirect('/admin/winkels');
    }

    // --- Zichtbaarheid ---

    public function test_new_store_form_has_visible_checked_and_featured_unchecked(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/winkels/create')
            ->assertOk()
            ->assertSee('id="is_visible" name="is_visible" value="1" checked', false)
            ->assertDontSee('id="is_featured" name="is_featured" value="1" checked', false);
    }

    public function test_unchecking_visible_really_saves_false_and_can_be_turned_back_on(): void
    {
        $store = Store::create($this->validData());

        // Uitgevinkt: het formulier stuurt dan alleen het verborgen veld met "0".
        $this->actingAs($this->admin())
            ->put("/admin/winkels/{$store->id}", $this->validData(['is_visible' => '0']))
            ->assertRedirect('/admin/winkels');
        $this->assertFalse($store->fresh()->is_visible);

        $this->actingAs($this->admin())
            ->put("/admin/winkels/{$store->id}", $this->validData(['is_visible' => '1']));
        $this->assertTrue($store->fresh()->is_visible);
    }

    public function test_hidden_store_is_not_public_but_stays_in_admin(): void
    {
        Store::create($this->validData(['name' => 'Zichtbare Zaak', 'category' => 'mode', 'is_featured' => '1']));
        $hidden = Store::create($this->validData(['name' => 'Verborgen Boetiek', 'category' => 'mode', 'is_visible' => '0', 'is_featured' => '1']));

        $this->get('/winkels')->assertSee('Zichtbare Zaak')->assertDontSee('Verborgen Boetiek');
        $this->get('/winkels?categorie=mode')->assertSee('Zichtbare Zaak')->assertDontSee('Verborgen Boetiek');
        $this->get('/')->assertSee('Zichtbare Zaak')->assertDontSee('Verborgen Boetiek');
        $this->get('/home')->assertSee('Zichtbare Zaak')->assertDontSee('Verborgen Boetiek');

        $this->actingAs($this->admin())->get('/admin/winkels')->assertSee('Verborgen Boetiek')->assertSee('admin-status--hidden">Verborgen', false);
        $this->actingAs($this->admin())->get("/admin/winkels/{$hidden->id}/edit")->assertOk()->assertSee('Verborgen Boetiek');
    }

    public function test_checkbox_choice_is_kept_after_a_validation_error(): void
    {
        $this->actingAs($this->admin())
            ->from('/admin/winkels/create')
            ->post('/admin/winkels', $this->validData(['name' => '', 'is_visible' => '0', 'is_featured' => '1']))
            ->assertRedirect('/admin/winkels/create')
            ->assertSessionHasErrors('name');

        // Het formulier opnieuw openen (met de oude invoer uit de sessie).
        $this->actingAs($this->admin())
            ->get('/admin/winkels/create')
            ->assertDontSee('id="is_visible" name="is_visible" value="1" checked', false)
            ->assertSee('id="is_featured" name="is_featured" value="1" checked', false);
    }

    // --- Uitgelichte winkels op de homepage ---

    public function test_homepage_shows_at_most_three_visible_featured_stores_alphabetically(): void
    {
        foreach (['Winkel Echo', 'Winkel Alfa', 'Winkel Delta', 'Winkel Bravo', 'Winkel Charlie'] as $name) {
            Store::create($this->validData(['name' => $name, 'is_featured' => '1']));
        }
        Store::create($this->validData(['name' => 'Winkel Aaa Verborgen', 'is_featured' => '1', 'is_visible' => '0']));
        Store::create($this->validData(['name' => 'Winkel Niet Uitgelicht']));

        foreach (['/', '/home'] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('Uitgelichte Winkels')
                ->assertSeeInOrder(['Winkel Alfa', 'Winkel Bravo', 'Winkel Charlie'])
                ->assertDontSee('Winkel Delta')
                ->assertDontSee('Winkel Echo')
                ->assertDontSee('Winkel Aaa Verborgen')
                ->assertDontSee('Winkel Niet Uitgelicht')
                ->assertDontSee('OPEN til');
        }
    }

    public function test_featured_section_is_hidden_without_suitable_stores(): void
    {
        Store::create($this->validData(['name' => 'Gewone Winkel']));

        $this->get('/')->assertOk()->assertDontSee('Uitgelichte Winkels')->assertDontSee('Fashion District');
    }

    public function test_changes_to_a_featured_store_show_up_on_the_homepage(): void
    {
        $store = Store::create($this->validData(['name' => 'Oude Naam', 'is_featured' => '1']));

        $this->actingAs($this->admin())->put("/admin/winkels/{$store->id}", $this->validData(['name' => 'Nieuwe Naam', 'is_featured' => '1']));
        $this->get('/')->assertSee('Nieuwe Naam')->assertDontSee('Oude Naam');

        $this->actingAs($this->admin())->delete("/admin/winkels/{$store->id}");
        $this->get('/')->assertDontSee('Nieuwe Naam')->assertDontSee('Uitgelichte Winkels');
    }

    // --- Zoeken in het beheer ---

    public function test_admin_can_search_stores_by_name(): void
    {
        Store::create($this->validData(['name' => 'Albert Heijn']));
        Store::create($this->validData(['name' => 'Bakker Bart']));
        Store::create($this->validData(['name' => 'Verborgen Boetiek', 'is_visible' => '0']));

        // Hoofdletterongevoelig, en de zoekterm blijft in het zoekveld staan.
        $this->actingAs($this->admin())
            ->get('/admin/winkels?zoek=ALBERT')
            ->assertOk()
            ->assertSee('Albert Heijn')
            ->assertDontSee('Bakker Bart')
            ->assertSee('value="ALBERT"', false)
            ->assertSee('Zoekopdracht wissen');

        // Verborgen winkels worden ook gevonden.
        $this->actingAs($this->admin())->get('/admin/winkels?zoek=boetiek')->assertSee('Verborgen Boetiek');

        // Lege zoekterm: alle winkels.
        $this->actingAs($this->admin())
            ->get('/admin/winkels?zoek=')
            ->assertSee('Albert Heijn')
            ->assertSee('Bakker Bart')
            ->assertSee('Verborgen Boetiek');
    }

    public function test_search_without_results_shows_message_and_way_back(): void
    {
        Store::create($this->validData(['name' => 'Albert Heijn']));

        $this->actingAs($this->admin())
            ->get('/admin/winkels?zoek=xyz')
            ->assertOk()
            ->assertSee('Geen winkels gevonden')
            ->assertSee('Toon alle winkels')
            ->assertDontSee('Albert Heijn');

        // Een rare zoekparameter (array) geeft geen serverfout.
        $this->actingAs($this->admin())->get('/admin/winkels?zoek[]=x')->assertOk()->assertSee('Albert Heijn');
    }
}
