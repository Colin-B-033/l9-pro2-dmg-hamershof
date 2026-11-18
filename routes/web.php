<?php

use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\StoreController;
use App\Models\Event;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Homepage met maximaal drie zichtbare, uitgelichte winkels (alfabetisch)
// en maximaal drie aankomende evenementen (zelfde keuze en volgorde als op /events).
$homePage = function () {
    $featuredStores = Store::visible()
        ->where('is_featured', true)
        ->orderBy('name')
        ->limit(3)
        ->get();

    $upcomingEvents = Event::visible()
        ->upcoming()
        ->inAgendaOrder()
        ->limit(3)
        ->get();

    return view('home', ['featuredStores' => $featuredStores, 'upcomingEvents' => $upcomingEvents]);
};

Route::get('/', $homePage)->name('home');

// Het menu linkt naar /home; dat adres toont precies dezelfde homepage.
Route::get('/home', $homePage);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function (Request $request) {
        // Beheerders gaan na het inloggen direct naar het CMS.
        if ($request->user()->is_admin) {
            return redirect()->route('admin.stores.index');
        }

        return Inertia::render('Dashboard');
    })->name('dashboard');
});

// Openbare winkelpagina met de winkels uit het CMS.
Route::get('/stores', function (Request $request) {
    // Optioneel filter via ?categorie=mode; onbekende waarden worden genegeerd.
    $category = $request->query('categorie');
    if (! is_string($category) || ! isset(Store::CATEGORIES[$category])) {
        $category = null;
    }

    $stores = Store::visible()
        ->when($category, fn ($query) => $query->where('category', $category))
        ->orderBy('name')
        ->get();

    return view('stores', [
        'stores' => $stores,
        'activeCategory' => $category,
    ]);
})->name('stores');

// Oud adres van de winkelpagina: doorsturen naar /stores (met hetzelfde categoriefilter).
Route::get('/winkels', function (Request $request) {
    $category = $request->query('categorie');

    return redirect()->route('stores', is_string($category) ? ['categorie' => $category] : [], 301);
});

// Openbare evenementenpagina: zichtbare evenementen van vandaag of later, met zoeken via ?zoek=...
Route::get('/events', function (Request $request) {
    // Alleen een gewone tekst gebruiken; iets als ?zoek[]=test wordt genegeerd.
    $search = $request->query('zoek');
    $search = is_string($search) ? trim($search) : '';

    $events = Event::visible()
        ->upcoming()
        // whereLike zoekt standaard hoofdletterongevoelig en gebruikt een gebonden parameter.
        ->when($search !== '', fn ($query) => $query->whereLike('title', "%{$search}%"))
        ->inAgendaOrder()
        ->get();

    return view('events', ['events' => $events, 'search' => $search]);
})->name('events');

Route::get('/bezoekons', function () {
    return view('bezoekons');
});

Route::get('/tehuur', function () {
    return view('tehuur');
});

Route::get('/plattegrond', function () {
    return view('plattegrond', ['shops' => config('map-shops')]);
});

// Openbare agenda: de zichtbare evenementen uit het CMS gaan als JSON mee naar public/js/agenda.js.
Route::get('/agenda', function () {
    $events = Event::visible()
        ->inAgendaOrder()
        ->get()
        ->map(fn (Event $event) => $event->toAgendaArray());

    return view('agenda', ['events' => $events]);
})->name('agenda');

// CMS: alleen voor ingelogde beheerders (is_admin = true).
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/winkels')->name('index');

    Route::resource('winkels', StoreController::class)
        ->parameters(['winkels' => 'store'])
        ->names('stores')
        ->except('show');

    Route::resource('evenementen', EventController::class)
        ->parameters(['evenementen' => 'event'])
        ->names('events')
        ->except('show');
});
