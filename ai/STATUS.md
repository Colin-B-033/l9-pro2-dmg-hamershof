# Status CMS Hamershof – tussenoplevering

Eerste werkende CMS-onderdeel: **winkelbeheer**, gekoppeld aan de openbare pagina `/winkels`.

## Wat nu werkt

- Beheeromgeving onder `/admin` (Blade, in Hamershof-huisstijl), via de bestaande Fortify-login.
- Winkels: overzicht, toevoegen, bewerken, verwijderen (met bevestiging), succesmeldingen.
- Validatie met Nederlandse foutmeldingen; ingevulde velden blijven staan bij een fout.
- Velden: naam, categorie (vaste lijst), omschrijving, locatie, website (alleen http/https), telefoon.
- `/winkels` toont de winkels uit de database, met categoriefilter (`?categorie=mode`), lege toestand en een afbeeldingfallback (beginletter).
- Beveiliging: routes achter `auth` + `admin`-middleware (`users.is_admin`). Gewone gebruikers krijgen 403. `is_admin` staat niet in `$fillable`, dus registratie kan het niet zetten. CSRF op alle formulieren, tekst wordt ge-escaped.
- Reparaties: `/` toont de Hamershof-homepage (`/home` werkt ook), `Home.mp4` staat in de Vite-build, links naar Winkels in menu en footer, inloglink wordt "Naar beheer" voor beheerders, beheerders komen na inloggen direct in het CMS.

## Belangrijkste bestanden

| Bestand | Functie |
| --- | --- |
| `routes/web.php` | Openbare routes, `/winkels`, beheerroutes onder `/admin` |
| `routes/console.php` | Commando `admin:create` voor een beheeraccount |
| `app/Http/Controllers/Admin/StoreController.php` | CRUD + validatie voor winkels |
| `app/Http/Middleware/EnsureUserIsAdmin.php` | Beheercontrole (alias `admin` in `bootstrap/app.php`) |
| `app/Models/Store.php` | Winkelmodel en vaste categorielijst |
| `database/migrations/2026_11_04_*` | `is_admin` op users, tabel `stores` |
| `database/seeders/StoreSeeder.php` | 8 voorbeeldwinkels (herhaalbaar, maakt geen dubbele aan) |
| `resources/views/admin/*` | Beheerlayout, overzicht en formulier |
| `resources/css/admin.css` | Stijl van het beheer (eigen Vite-entry) |
| `resources/views/stores.blade.php` | Openbare winkelpagina |
| `tests/Feature/StoreManagementTest.php` | Featuretests voor het nieuwe gedrag |

## Lokaal starten en demonstreren

Eenmalig (bij een nieuwe checkout):

```bash
composer install
npm ci
cp .env.example .env        # daarna APP_URL aanpassen, bijv. http://127.0.0.1:8123
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan db:seed --class=StoreSeeder
npm run build
```

Starten: `php artisan serve --port=8123` en open `http://127.0.0.1:8123`.
Beheer: `http://127.0.0.1:8123/admin` (of via "Winkelier inloggen" in het menu).

Demo: inloggen → winkel bewerken of toevoegen → `/winkels` vernieuwen → wijziging zichtbaar.

## Beheeraccount aanmaken

```bash
php artisan admin:create beheer@hamershof.test --name="Beheer Hamershof"
```

Bij een nieuw account wordt een willekeurig wachtwoord gegenereerd en één keer in de terminal getoond.
Bestaat het e-mailadres al, dan krijgt dat account alleen beheerrechten (wachtwoord blijft gelijk).

## Uitgevoerde controles (4 november 2026)

- `php artisan test`: 47 geslaagd, 7 overgeslagen (Jetstream-API-tests; API-feature staat uit). Tests gebruiken SQLite in het geheugen.
- `npm run build` slaagt; `resources/assets/Home.mp4` staat in de manifest.
- Via HTTP op de lokale server: home, `/home`, `/winkels`, login, events en bezoek-ons geven 200; gast wordt bij `/admin` naar login gestuurd; gewone gebruiker krijgt 403; beheerder kan toevoegen, wijzigen en verwijderen en dat is zichtbaar op `/winkels`; ongeldige invoer wordt geweigerd; POST zonder CSRF-token geeft 419.
- Migraties heen en terug getest (`migrate:rollback --step=2`) op een losse database.
- Schermafbeeldingen (headless Chrome) van home, winkels, login en beheerschermen, desktop en smal scherm (500px).
- Niet getest: de bevestigingspop-up van de browser bij verwijderen (gewone `confirm()`), en echte mobiele apparaten.

## Bewust nog niet in deze versie (vervolgwerk)

- Beheer van nieuws, evenementen/agenda, huurpanden en plattegrond.
- Afbeeldingen/logo uploaden, openingstijden, winkeldetailpagina, categorieën als aparte tabel, paginering in het beheer.
- Meerdere rollen (bijv. winkelier die alleen de eigen winkel beheert); nu alleen `is_admin`.
- Nederlandse vertaling van de Fortify/Jetstream-meldingen (bijv. foutmelding bij inloggen).
- Openbare pagina's die nog ontbreken: menu en footer linken naar o.a. `/te-huur`, `/plattegrond`, `/nieuws`, `/agenda`, `/markten`, `/hamershofbon`, `/contact`, `/privacy`, `/cookies` (404). `/events` en `/bezoekons` bestaan wel, maar staan niet in het menu.
- Bestaand ontwerp: zeer kleine tekst op de winkelkaarten (0,42rem), lege `<img src="">` op de homepage, homepage-evenementen en uitgelichte winkels zijn nog vaste voorbeeldtekst.
- `db.sql` is alleen als inhoudelijke referentie gebruikt, niet geïmporteerd.
