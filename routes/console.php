<?php

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Maakt een beheerder voor het CMS, of geeft een bestaand account beheerrechten.
 * Voorbeeld: php artisan admin:create beheer@hamershof.test --name="Beheer Hamershof"
 * Bij een nieuw account wordt een willekeurig wachtwoord gegenereerd en één keer getoond.
 */
Artisan::command('admin:create {email} {--name=Beheerder}', function (string $email) {
    $user = User::where('email', $email)->first();

    if ($user) {
        $user->forceFill(['is_admin' => true])->save();
        $this->info("Bestaand account {$email} is nu beheerder. Het wachtwoord is niet gewijzigd.");

        return;
    }

    $password = Str::password(16, symbols: false);

    // Zelfde code als bij registratie, zodat het account ook een persoonlijk team krijgt.
    $user = app(CreateNewUser::class)->create([
        'name' => $this->option('name'),
        'email' => $email,
        'password' => $password,
        'password_confirmation' => $password,
    ]);
    $user->forceFill(['is_admin' => true, 'email_verified_at' => now()])->save();

    $this->info("Beheerder {$email} aangemaakt.");
    $this->line("Wachtwoord (wordt maar één keer getoond): {$password}");
})->purpose('Maak een CMS-beheerder aan of geef een bestaand account beheerrechten');
