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

### Toegevoegd op 5 november 2026

- **Zichtbaar op de website** (`stores.is_visible`, standaard aan): een verborgen winkel blijft in het beheer bestaan en bewerkbaar, maar staat niet op `/winkels` (ook niet via een categoriefilter) en niet op de homepage. Bestaande winkels bleven na de migratie zichtbaar.
- **Uitlichten op de homepage** (`stores.is_featured`, standaard uit): het blok "Uitgelichte Winkels" op `/` en `/home` toont maximaal drie zichtbare, uitgelichte winkels op alfabetische volgorde. Zonder geschikte winkels wordt de kop met kaarten verborgen; alleen de knop "Bekijk Alle Winkels" blijft staan. De verzonnen openingstijden zijn uit de kaarten gehaald.
- Checkboxes werken met een verborgen veld `0` + checkbox `1`, zodat uitvinken echt `false` opslaat en de keuze bewaard blijft bij een validatiefout.
- **Zoeken in het beheer** op winkelnaam via `?zoek=...`: hoofdletterongevoelig (`whereLike`, gebonden parameter), vindt ook verborgen winkels, met een melding en "Toon alle winkels" bij geen resultaten.
- De beheertabel toont per winkel "Zichtbaar"/"Verborgen" en "Uitgelicht".

## Belangrijkste bestanden

| Bestand | Functie |
| --- | --- |
| `routes/web.php` | Homepage (`/` en `/home`, met uitgelichte winkels), `/winkels`, beheerroutes onder `/admin` |
| `routes/console.php` | Commando `admin:create` voor een beheeraccount |
| `app/Http/Controllers/Admin/StoreController.php` | CRUD, zoeken en validatie voor winkels |
| `app/Http/Middleware/EnsureUserIsAdmin.php` | Beheercontrole (alias `admin` in `bootstrap/app.php`) |
| `app/Models/Store.php` | Winkelmodel, categorielijst, casts en scope `visible()` |
| `database/migrations/2026_11_04_*` | `is_admin` op users, tabel `stores` |
| `database/migrations/2026_11_05_000001_*` | `is_visible` en `is_featured` op `stores` |
| `database/seeders/StoreSeeder.php` | 8 voorbeeldwinkels, waarvan 3 uitgelicht (herhaalbaar, overschrijft bestaande winkels niet) |
| `resources/views/admin/*` | Beheerlayout, overzicht met zoeken, formulier met checkboxes |
| `resources/css/admin.css` | Stijl van het beheer (eigen Vite-entry) |
| `resources/views/stores.blade.php` | Openbare winkelpagina |
| `resources/views/home.blade.php` + `resources/css/home.css` | Blok "Uitgelichte Winkels" uit de database (nette indeling bij 1 of 2 kaarten) |
| `tests/Feature/StoreManagementTest.php` | Featuretests voor winkelbeheer, zichtbaarheid, uitlichten en zoeken |

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

Demo basis: inloggen → winkel bewerken of toevoegen → `/winkels` vernieuwen → wijziging zichtbaar.

Demo zichtbaarheid en uitlichten:

1. Beheer → bij een winkel **Bewerken** → vink **Uitlichten op de homepage** aan → opslaan. In de tabel staat nu "Uitgelicht".
2. Open `/` en vernieuw: de winkel staat onder "Uitgelichte Winkels" (maximaal drie, alfabetisch).
3. Bewerk dezelfde winkel opnieuw → vink **Zichtbaar op de website** uit → opslaan. In de tabel staat "Verborgen".
4. Vernieuw `/` en `/winkels`: de winkel is weg. In het beheer staat hij er nog; zoek bijvoorbeeld op een deel van de naam.

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

## Uitgevoerde controles (5 november 2026)

- `php artisan test --filter=StoreManagementTest`: 20 geslaagd. Daarna de volledige suite: 56 geslaagd, 7 overgeslagen (dezelfde Jetstream-API-tests).
  - Nieuwe fout tijdens het werk: de standaard `ExampleTest` opent `/`, en die leest nu uit de database. Opgelost door daar `RefreshDatabase` aan te zetten (de regel stond er al als commentaar). Er is geen test uitgezet.
- `npm run build` slaagt; `pint --test` op de gewijzigde PHP-bestanden slaagt.
- Nieuwe migratie heen en terug getest op een losse database; op de ontwikkeldatabase bleven alle 8 winkels zichtbaar.
- Via HTTP op de lokale server, met het bestaande beheeraccount en twee tijdelijke controlewinkels (daarna via het CMS verwijderd):
  - twee uitgelicht → twee kaarten op `/` en `/home`, zonder openingstijden;
  - verbergen slaat `is_visible = 0` op → weg van `/`, `/home`, `/winkels` en `/winkels?categorie=diensten`, wel vindbaar en bewerkbaar in het beheer;
  - zoeken op "CONTROLE", "atelier", "xyz" (geen resultaten) en een lege zoekterm;
  - validatiefout houdt de checkboxkeuze vast;
  - zonder uitgelichte winkels verdwijnt het blok van de homepage.
- Schermafbeeldingen (headless Chrome, desktop en 500px breed) van de homepage met twee kaarten, het zoekresultaat, het overzicht en het formulier met validatiefout.
- Beperking: headless Chrome speelt de video in de kaarten onder de vouw niet af (ook niet bij de bestaande evenementkaarten), dus die zijn in de schermafbeeldingen grijs.

## Bewust nog niet in deze versie (vervolgwerk)

- Beheer van nieuws, evenementen/agenda, huurpanden en plattegrond.
- Afbeeldingen/logo uploaden, openingstijden, winkeldetailpagina, categorieën als aparte tabel, paginering in het beheer.
- Meerdere rollen (bijv. winkelier die alleen de eigen winkel beheert); nu alleen `is_admin`.
- Nederlandse vertaling van de Fortify/Jetstream-meldingen (bijv. foutmelding bij inloggen).
- Openbare pagina's die nog ontbreken: menu en footer linken naar o.a. `/te-huur`, `/plattegrond`, `/nieuws`, `/agenda`, `/markten`, `/hamershofbon`, `/contact`, `/privacy`, `/cookies` (404). `/events` en `/bezoekons` bestaan wel, maar staan niet in het menu.
- Bestaand ontwerp: zeer kleine tekst op de winkelkaarten (0,42rem), lege `<img src="">` op de homepage, evenementen op de homepage en de categorietellingen ("18 winkels") zijn nog vaste voorbeeldtekst.
- Volgorde van uitgelichte winkels zelf kiezen (nu alfabetisch), en een waarschuwing in het beheer als meer dan drie winkels zijn uitgelicht.
- `db.sql` is alleen als inhoudelijke referentie gebruikt, niet geïmporteerd.
