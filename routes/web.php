<?php

use App\Http\Controllers\Admin\StoreController;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Homepage met maximaal drie zichtbare, uitgelichte winkels (alfabetisch).
$homePage = function () {
    $featuredStores = Store::visible()
        ->where('is_featured', true)
        ->orderBy('name')
        ->limit(3)
        ->get();

    return view('home', ['featuredStores' => $featuredStores]);
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

Route::get('/events', function () {
    return view('events');
});

Route::get('/bezoekons', function () {
    return view('bezoekons');
});

Route::get('/tehuur', function () {
    return view('tehuur');
});

Route::get('/plattegrond', function () {
    return view('plattegrond', ['shops' => config('map-shops')]);
});

Route::get('/agenda', function () {
    return view('agenda');
});

// CMS: alleen voor ingelogde beheerders (is_admin = true).
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/winkels')->name('index');

    Route::resource('winkels', StoreController::class)
        ->parameters(['winkels' => 'store'])
        ->names('stores')
        ->except('show');
});
