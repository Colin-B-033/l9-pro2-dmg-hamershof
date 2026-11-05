<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

        // Nep-opslag: uploads in de tests komen nooit in de echte storage-map.
        Storage::fake('public');
    }

    /** Tijdelijke bestanden die in tearDown() weer worden opgeruimd. */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        array_map('unlink', array_filter($this->tempFiles, 'is_file'));

        parent::tearDown();
    }

    /**
     * Maakt een upload van een echt tijdelijk bestand met de opgegeven inhoud.
     * Let op: UploadedFile::fake() leidt het type af van de bestandsnaam. Een echte upload
     * wordt op inhoud gecontroleerd, en dat willen we hier ook testen.
     */
    private function upload(string $name, string $content): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'winkel-test-');
        file_put_contents($path, $content);
        $this->tempFiles[] = $path;

        return new UploadedFile($path, $name, null, null, true);
    }

    /** Een echte (piepkleine) PNG; desgewenst groter gemaakt met extra bytes. */
    private function pngUpload(string $name = 'winkel.png', int $extraBytes = 0): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

        return $this->upload($name, $png.str_repeat("\0", $extraBytes));
    }

    private function webpUpload(string $name = 'logo.webp'): UploadedFile
    {
        return $this->upload($name, base64_decode('UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4HAA=='));
    }

    /**
     * Maakt via het CMS een winkel met afbeelding en geeft de winkel terug.
     */
    private function createStoreWithImage(array $overrides = []): Store
    {
        $this->actingAs($this->admin())
            ->post('/admin/winkels', $this->validData(['image' => $this->pngUpload()] + $overrides))
            ->assertSessionHasNoErrors();

        return Store::latest('id')->firstOrFail();
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

    // --- Afbeelding per winkel ---

    public function test_valid_image_is_stored_with_generated_name_and_shown_publicly(): void
    {
        $store = $this->createStoreWithImage(['is_featured' => '1']);

        $this->assertStringStartsWith('stores/', $store->image_path);
        $this->assertStringNotContainsString('winkel.png', $store->image_path); // gegenereerde naam
        Storage::disk('public')->assertExists($store->image_path);

        $this->get('/winkels')->assertOk()->assertSee($store->image_path);
        $this->get('/')->assertOk()->assertSee($store->image_path);
        $this->actingAs($this->admin())->get("/admin/winkels/{$store->id}/edit")->assertSee($store->image_path);
    }

    public function test_jpg_and_webp_are_accepted(): void
    {
        $jpg = $this->upload('foto.jpg', base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA='));

        foreach (['Foto Winkel' => $jpg, 'Logo Winkel' => $this->webpUpload()] as $name => $file) {
            $this->actingAs($this->admin())
                ->post('/admin/winkels', $this->validData(['name' => $name, 'image' => $file]))
                ->assertSessionHasNoErrors();

            Storage::disk('public')->assertExists(Store::where('name', $name)->value('image_path'));
        }
    }

    public function test_invalid_svg_and_too_large_files_are_rejected(): void
    {
        $wrongType = 'Kies een afbeelding van het type JPG, PNG of WebP.';

        $cases = [
            // Tekstbestand met een afbeeldingsnaam: de inhoud telt, niet de naam.
            [$this->upload('nep.jpg', 'dit is geen afbeelding'), $wrongType],
            // SVG kan scripts bevatten en is daarom niet toegestaan.
            [$this->upload('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'), $wrongType],
            // Een echte PNG, maar net groter dan 2 MB.
            [$this->pngUpload('groot.png', 2 * 1024 * 1024), 'De afbeelding mag maximaal 2 MB groot zijn.'],
        ];

        foreach ($cases as [$file, $expectedMessage]) {
            $this->actingAs($this->admin())
                ->post('/admin/winkels', $this->validData(['image' => $file]))
                ->assertSessionHasErrors(['image' => $expectedMessage]);
        }

        $this->assertDatabaseCount('stores', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_validation_error_keeps_the_existing_image(): void
    {
        $store = $this->createStoreWithImage();
        $oldPath = $store->image_path;

        // Ander veld ongeldig, met een geldige nieuwe upload erbij.
        $this->actingAs($this->admin())
            ->put("/admin/winkels/{$store->id}", $this->validData(['name' => '', 'image' => $this->pngUpload('nieuw.png')]))
            ->assertSessionHasErrors('name');

        // Ongeldige upload, met "afbeelding verwijderen" aangevinkt.
        $this->actingAs($this->admin())
            ->put("/admin/winkels/{$store->id}", $this->validData([
                'image' => $this->upload('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'),
                'remove_image' => '1',
            ]))
            ->assertSessionHasErrors('image');

        $this->assertSame($oldPath, $store->fresh()->image_path);
        Storage::disk('public')->assertExists($oldPath);
        $this->assertCount(1, Storage::disk('public')->allFiles());
    }

    public function test_replacing_an_image_deletes_only_the_old_file_of_that_store(): void
    {
        Storage::disk('public')->put('logo.png', 'gedeeld bestand buiten de winkelmap');
        $other = $this->createStoreWithImage(['name' => 'Andere Winkel']);
        $store = $this->createStoreWithImage();
        $oldPath = $store->image_path;

        $this->actingAs($this->admin())
            ->put("/admin/winkels/{$store->id}", $this->validData(['image' => $this->webpUpload()]))
            ->assertSessionHasNoErrors();

        $store->refresh();
        $this->assertNotSame($oldPath, $store->image_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($store->image_path);
        Storage::disk('public')->assertExists($other->image_path);
        Storage::disk('public')->assertExists('logo.png');
    }

    public function test_removing_an_image_deletes_the_file_and_shows_the_fallback(): void
    {
        $store = $this->createStoreWithImage();
        $oldPath = $store->image_path;

        $this->actingAs($this->admin())
            ->put("/admin/winkels/{$store->id}", $this->validData(['remove_image' => '1']))
            ->assertSessionHasNoErrors();

        $this->assertNull($store->fresh()->image_path);
        Storage::disk('public')->assertMissing($oldPath);
        $this->get('/winkels')->assertOk()->assertSee('store-card__image--fallback', false)->assertDontSee($oldPath);
    }

    public function test_new_upload_wins_over_remove_image(): void
    {
        $store = $this->createStoreWithImage();
        $oldPath = $store->image_path;

        $this->actingAs($this->admin())
            ->put("/admin/winkels/{$store->id}", $this->validData(['image' => $this->webpUpload(), 'remove_image' => '1']))
            ->assertSessionHasNoErrors();

        $store->refresh();
        $this->assertNotNull($store->image_path);
        Storage::disk('public')->assertExists($store->image_path);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_deleting_a_store_deletes_only_its_own_image(): void
    {
        $other = $this->createStoreWithImage(['name' => 'Andere Winkel']);
        $store = $this->createStoreWithImage();

        $this->actingAs($this->admin())->delete("/admin/winkels/{$store->id}")->assertRedirect('/admin/winkels');

        Storage::disk('public')->assertMissing($store->image_path);
        Storage::disk('public')->assertExists($other->image_path);
    }

    public function test_image_path_cannot_be_sent_from_the_form(): void
    {
        Storage::disk('public')->put('logo.png', 'gedeeld bestand');

        $this->actingAs($this->admin())->post('/admin/winkels', $this->validData(['image_path' => 'logo.png']));
        $store = Store::firstOrFail();
        $this->assertNull($store->image_path);

        // Ook bij wijzigen en verwijderen wordt een meegestuurd pad genegeerd.
        $this->actingAs($this->admin())->put("/admin/winkels/{$store->id}", $this->validData(['image_path' => 'logo.png', 'remove_image' => '1']));
        $this->actingAs($this->admin())->delete("/admin/winkels/{$store->id}");
        Storage::disk('public')->assertExists('logo.png');
    }

    public function test_store_without_image_or_with_missing_file_shows_fallback(): void
    {
        Store::create($this->validData(['name' => 'Zonder Afbeelding', 'is_featured' => '1']));
        $missing = Store::create($this->validData(['name' => 'Kwijt Bestand', 'is_featured' => '1']));
        $missing->forceFill(['image_path' => 'stores/bestaat-niet.png'])->save();

        $this->get('/winkels')
            ->assertOk()
            ->assertSee('Zonder Afbeelding')
            ->assertSee('Kwijt Bestand')
            ->assertDontSee('bestaat-niet.png')
            ->assertDontSee('store-card__image--photo', false);

        $this->get('/')->assertOk()->assertSee('Kwijt Bestand')->assertDontSee('bestaat-niet.png');

        $this->actingAs($this->admin())->get("/admin/winkels/{$missing->id}/edit")
            ->assertOk()
            ->assertSee('Het afbeeldingsbestand van deze winkel ontbreekt.');
    }

    // --- Openingstijden ---

    public function test_opening_hours_are_saved_shown_safely_and_can_be_cleared(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/winkels', $this->validData(['opening_hours' => 'Ma–vr 09:00–18:00, za 09:00–17:00']))
            ->assertSessionHasNoErrors();
        $store = Store::firstOrFail();

        $this->get('/winkels')->assertSee('Openingstijden:')->assertSee('Ma–vr 09:00–18:00, za 09:00–17:00');

        // Tekst wordt veilig weergegeven, niet als HTML.
        $this->actingAs($this->admin())->put("/admin/winkels/{$store->id}", $this->validData(['opening_hours' => '<b>Altijd</b> open']));
        $this->get('/winkels')->assertDontSee('<b>Altijd</b>', false)->assertSee('&lt;b&gt;Altijd&lt;/b&gt; open', false);

        // Te lang wordt geweigerd.
        $this->actingAs($this->admin())
            ->put("/admin/winkels/{$store->id}", $this->validData(['opening_hours' => str_repeat('a', 256)]))
            ->assertSessionHasErrors('opening_hours');

        // Leegmaken: daarna wordt er niets getoond.
        $this->actingAs($this->admin())->put("/admin/winkels/{$store->id}", $this->validData(['opening_hours' => '']));
        $this->assertNull($store->fresh()->opening_hours);
        $this->get('/winkels')->assertDontSee('Openingstijden:');
    }

    // --- Tellers in het beheer ---

    public function test_counters_describe_all_stores_and_do_not_change_when_searching(): void
    {
        Store::create($this->validData(['name' => 'Albert Heijn']));
        Store::create($this->validData(['name' => 'Bakker Bart']));
        Store::create($this->validData(['name' => 'Verborgen Boetiek', 'is_visible' => '0']));

        $counts = ['<dt>Totaal</dt><dd>3</dd>', '<dt>Zichtbaar</dt><dd>2</dd>', '<dt>Verborgen</dt><dd>1</dd>'];

        foreach (['/admin/winkels', '/admin/winkels?zoek=albert', '/admin/winkels?zoek=xyz'] as $url) {
            $this->actingAs($this->admin())->get($url)->assertOk()->assertSeeInOrder($counts, false);
        }

        // Het aantal zoekresultaten staat er apart onder.
        $this->actingAs($this->admin())->get('/admin/winkels?zoek=albert')->assertSee('1 winkel gevonden voor');
    }

    // --- Uploads alleen voor beheerders ---

    public function test_guests_and_regular_users_cannot_upload_or_change_images(): void
    {
        $store = $this->createStoreWithImage();
        $path = $store->image_path;
        $user = User::factory()->create();

        auth()->logout();
        $this->post('/admin/winkels', $this->validData(['name' => 'Gast', 'image' => $this->pngUpload()]))->assertRedirect('/login');

        $this->actingAs($user)->post('/admin/winkels', $this->validData(['name' => 'Gebruiker', 'image' => $this->pngUpload()]))->assertForbidden();
        $this->actingAs($user)->put("/admin/winkels/{$store->id}", $this->validData(['image' => $this->webpUpload()]))->assertForbidden();
        $this->actingAs($user)->put("/admin/winkels/{$store->id}", $this->validData(['remove_image' => '1']))->assertForbidden();
        $this->actingAs($user)->delete("/admin/winkels/{$store->id}")->assertForbidden();

        $this->assertDatabaseCount('stores', 1);
        $this->assertSame($path, $store->fresh()->image_path);
        $this->assertSame([$path], Storage::disk('public')->allFiles());
    }
}
