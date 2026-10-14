<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::get('/home', function () {
    return view('home');
});

Route::view('/plattegrond', 'plattegrond-page')->name('plattegrond');

Route::get('/plattegrond/ontwerpen/{design?}', function ($design = '1') {
    abort_unless(in_array($design, ['1', '2', '3'], true), 404);
    return view('map-designs.index', ['design' => $design, 'shops' => config('map-shops')]);
})->name('map-designs');

Route::get('/winkels', function () {
    return view('stores');
});

Route::get('/events', function () {
    return view('events');
});
