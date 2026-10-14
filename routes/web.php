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

Route::get('/plattegrond', function () {
    return view('plattegrond', ['shops' => config('map-shops')]);
})->name('plattegrond');

Route::redirect('/plattegrond/ontwerpen/{design?}', '/plattegrond');

Route::get('/winkels', function () {
    return view('stores');
});

Route::get('/events', function () {
    return view('events');
});

Route::view('/agenda', 'agenda')->name('agenda');
