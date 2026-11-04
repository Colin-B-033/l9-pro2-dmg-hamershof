<?php

use App\Http\Controllers\Admin\StoreController;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return view('home');
})->name('home');

// Oude adres van de homepage blijft werken.
Route::get('/home', function () {
    return view('home');
});

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

Route::get('/winkels', function (Request $request) {
    // Optioneel filter via ?categorie=mode; onbekende waarden worden genegeerd.
    $category = $request->query('categorie');
    if (! is_string($category) || ! isset(Store::CATEGORIES[$category])) {
        $category = null;
    }

    $stores = Store::query()
        ->when($category, fn ($query) => $query->where('category', $category))
        ->orderBy('name')
        ->get();

    return view('stores', [
        'stores' => $stores,
        'activeCategory' => $category,
    ]);
})->name('stores');

Route::get('/events', function () {
    return view('events');
});

Route::get('/bezoekons', function () {
    return view('bezoekons');
});

// CMS: alleen voor ingelogde beheerders (is_admin = true).
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/winkels')->name('index');

    Route::resource('winkels', StoreController::class)
        ->parameters(['winkels' => 'store'])
        ->names('stores')
        ->except('show');
});
