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
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/winkels')->assertRedirect('/login');
        $this->post('/admin/winkels', $this->validData())->assertRedirect('/login');

        $this->assertDatabaseCount('stores', 0);
    }

    public function test_regular_user_has_no_access_to_admin(): void
    {
        $user = User::factory()->create();
        $store = Store::create($this->validData());

        $this->actingAs($user)->get('/admin/winkels')->assertForbidden();
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
}
