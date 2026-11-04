<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Winkelbeheer in het CMS (/admin/winkels).
 * De routes zijn beveiligd met de middleware 'auth' en 'admin' (zie routes/web.php).
 */
class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::orderBy('name')->get();

        return view('admin.stores.index', ['stores' => $stores]);
    }

    public function create()
    {
        return view('admin.stores.form', ['store' => new Store]);
    }

    public function store(Request $request)
    {
        $store = Store::create($this->validateStore($request));

        return redirect()->route('admin.stores.index')
            ->with('success', "Winkel \"{$store->name}\" is toegevoegd.");
    }

    public function edit(Store $store)
    {
        return view('admin.stores.form', ['store' => $store]);
    }

    public function update(Request $request, Store $store)
    {
        $store->update($this->validateStore($request));

        return redirect()->route('admin.stores.index')
            ->with('success', "Winkel \"{$store->name}\" is opgeslagen.");
    }

    public function destroy(Store $store)
    {
        $store->delete();

        return redirect()->route('admin.stores.index')
            ->with('success', "Winkel \"{$store->name}\" is verwijderd.");
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
        ];

        $messages = [
            'required' => ':Attribute is verplicht.',
            'max' => ':Attribute mag maximaal :max tekens bevatten.',
            'in' => 'Kies een geldige categorie.',
            'url' => 'Vul een volledig webadres in, bijvoorbeeld https://www.voorbeeld.nl.',
            'phone.regex' => 'Een telefoonnummer mag alleen cijfers, spaties en + - ( ) bevatten.',
        ];

        $attributes = [
            'name' => 'naam',
            'category' => 'categorie',
            'description' => 'omschrijving',
            'location' => 'locatie',
            'website' => 'website',
            'phone' => 'telefoonnummer',
        ];

        return $request->validate($rules, $messages, $attributes);
    }
}
