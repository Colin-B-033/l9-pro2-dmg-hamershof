<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Winkelbeheer in het CMS (/admin/winkels).
 * De routes zijn beveiligd met de middleware 'auth' en 'admin' (zie routes/web.php).
 */
class StoreController extends Controller
{
    public function index(Request $request)
    {
        // Zoeken op naam via ?zoek=... (ook verborgen winkels worden gevonden).
        $search = $request->query('zoek');
        $search = is_string($search) ? trim($search) : '';

        $stores = Store::query()
            // whereLike zoekt standaard hoofdletterongevoelig en gebruikt een gebonden parameter.
            ->when($search !== '', fn ($query) => $query->whereLike('name', "%{$search}%"))
            ->orderBy('name')
            ->get();

        // Tellers over álle winkels, dus los van de zoekopdracht.
        $totalCount = Store::count();
        $visibleCount = Store::visible()->count();

        return view('admin.stores.index', [
            'stores' => $stores,
            'search' => $search,
            'totalCount' => $totalCount,
            'visibleCount' => $visibleCount,
            'hiddenCount' => $totalCount - $visibleCount,
        ]);
    }

    public function create()
    {
        return view('admin.stores.form', ['store' => new Store]);
    }

    public function store(Request $request)
    {
        $data = $this->validateStore($request);

        $store = new Store($data);

        if ($request->hasFile('image')) {
            // store() maakt zelf een willekeurige bestandsnaam met de juiste extensie.
            $store->image_path = $request->file('image')->store(Store::IMAGE_FOLDER, 'public');
        }

        $store->save();

        return redirect()->route('admin.stores.index')
            ->with('success', "Winkel \"{$store->name}\" is toegevoegd.");
    }

    public function edit(Store $store)
    {
        return view('admin.stores.form', ['store' => $store]);
    }

    public function update(Request $request, Store $store)
    {
        // Eerst valideren: bij een fout gebeurt er verder niets, ook niet met de huidige afbeelding.
        $data = $this->validateStore($request);

        $oldImagePath = $store->image_path;

        if ($request->hasFile('image')) {
            // Een nieuwe upload gaat altijd voor, ook als "afbeelding verwijderen" is aangevinkt.
            $store->image_path = $request->file('image')->store(Store::IMAGE_FOLDER, 'public');
        } elseif ($request->boolean('remove_image')) {
            $store->image_path = null;
        }

        $store->fill($data)->save();

        // Pas na het opslaan het oude bestand van déze winkel opruimen.
        if ($oldImagePath && $oldImagePath !== $store->image_path) {
            $this->deleteImage($oldImagePath);
        }

        return redirect()->route('admin.stores.index')
            ->with('success', "Winkel \"{$store->name}\" is opgeslagen.");
    }

    public function destroy(Store $store)
    {
        $store->delete();
        $this->deleteImage($store->image_path);

        return redirect()->route('admin.stores.index')
            ->with('success', "Winkel \"{$store->name}\" is verwijderd.");
    }

    /**
     * Verwijdert een winkelafbeelding, maar alleen als die in de winkelmap staat.
     * Zo kan deze code nooit andere bestanden of gedeelde assets weggooien.
     */
    private function deleteImage(?string $path): void
    {
        if ($path && str_starts_with($path, Store::IMAGE_FOLDER.'/') && ! str_contains($path, '..')) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Valideert het winkelformulier. Bij een fout stuurt Laravel de gebruiker
     * automatisch terug met de foutmeldingen en de ingevulde waarden (old()).
     */
    private function validateStore(Request $request): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', Rule::in(array_keys(Store::CATEGORIES))],
            'description' => ['nullable', 'string', 'max:1000'],
            'location' => ['nullable', 'string', 'max:150'],
            // Alleen http(s)-adressen, zodat er geen javascript:-links in de site komen.
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'opening_hours' => ['nullable', 'string', 'max:255'],
            'is_visible' => ['boolean'],
            'is_featured' => ['boolean'],
            // 'mimes' kijkt naar de inhoud van het bestand (niet naar de bestandsnaam).
            // SVG is bewust niet toegestaan, omdat daar scripts in kunnen zitten. max:2048 = 2 MB.
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['boolean'],
        ];

        $messages = [
            'required' => ':Attribute is verplicht.',
            'max' => ':Attribute mag maximaal :max tekens bevatten.',
            'in' => 'Kies een geldige categorie.',
            'url' => 'Vul een volledig webadres in, bijvoorbeeld https://www.voorbeeld.nl.',
            'phone.regex' => 'Een telefoonnummer mag alleen cijfers, spaties en + - ( ) bevatten.',
            'boolean' => 'Ongeldige keuze bij :attribute.',
            'image.file' => 'Kies een afbeelding van het type JPG, PNG of WebP.',
            'image.mimes' => 'Kies een afbeelding van het type JPG, PNG of WebP.',
            'image.max' => 'De afbeelding mag maximaal 2 MB groot zijn.',
            // Bijvoorbeeld als het bestand groter is dan PHP toestaat (upload_max_filesize).
            'image.uploaded' => 'De afbeelding kon niet worden geüpload. Kies een JPG, PNG of WebP van maximaal 2 MB.',
        ];

        $attributes = [
            'name' => 'naam',
            'category' => 'categorie',
            'description' => 'omschrijving',
            'location' => 'locatie',
            'website' => 'website',
            'phone' => 'telefoonnummer',
            'opening_hours' => 'openingstijden',
            'is_visible' => 'zichtbaar op de website',
            'is_featured' => 'uitlichten op de homepage',
            'image' => 'afbeelding',
            'remove_image' => 'afbeelding verwijderen',
        ];

        $data = $request->validate($rules, $messages, $attributes);

        // Checkboxes: het formulier stuurt "0" (verborgen veld) of "1" (aangevinkt).
        // $request->boolean() maakt daar altijd true of false van, dus uitvinken wordt echt false.
        $data['is_visible'] = $request->boolean('is_visible');
        $data['is_featured'] = $request->boolean('is_featured');

        // De afbeelding wordt hierboven apart verwerkt; die hoort niet in fill().
        unset($data['image'], $data['remove_image']);

        return $data;
    }
}
