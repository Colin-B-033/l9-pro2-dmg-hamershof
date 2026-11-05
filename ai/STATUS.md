# Status CMS Hamershof – tussenoplevering

Eerste werkende CMS-onderdeel: **winkelbeheer**, gekoppeld aan de openbare winkelpagina `/stores` (het oude adres `/winkels` stuurt door).

## Wat nu werkt

- Beheeromgeving onder `/admin` (Blade, in Hamershof-huisstijl), via de bestaande Fortify-login.
- Winkels: overzicht, toevoegen, bewerken, verwijderen (met bevestiging), succesmeldingen.
- Validatie met Nederlandse foutmeldingen; ingevulde velden blijven staan bij een fout.
- Velden: naam, categorie (vaste lijst), omschrijving, locatie, website (alleen http/https), telefoon.
- `/stores` toont de winkels uit de database, met categoriefilter (`?categorie=mode`), lege toestand en als fallback de standaardfoto van de website (`steen.png`).
- Beveiliging: routes achter `auth` + `admin`-middleware (`users.is_admin`). Gewone gebruikers krijgen 403. `is_admin` staat niet in `$fillable`, dus registratie kan het niet zetten. CSRF op alle formulieren, tekst wordt ge-escaped.
- Reparaties: `/` toont de Hamershof-homepage (`/home` werkt ook), `Home.mp4` staat in de Vite-build, links naar Winkels in menu en footer, inloglink wordt "Naar beheer" voor beheerders, beheerders komen na inloggen direct in het CMS.

### Toegevoegd op 5 november 2026

- **Zichtbaar op de website** (`stores.is_visible`, standaard aan): een verborgen winkel blijft in het beheer bestaan en bewerkbaar, maar staat niet op `/stores` (ook niet via een categoriefilter) en niet op de homepage. Bestaande winkels bleven na de migratie zichtbaar.
- **Uitlichten op de homepage** (`stores.is_featured`, standaard uit): het blok "Uitgelichte Winkels" op `/` en `/home` toont maximaal drie zichtbare, uitgelichte winkels op alfabetische volgorde. Zonder geschikte winkels wordt de kop met kaarten verborgen; alleen de knop "Bekijk Alle Winkels" blijft staan. De verzonnen openingstijden zijn uit de kaarten gehaald.
- Checkboxes werken met een verborgen veld `0` + checkbox `1`, zodat uitvinken echt `false` opslaat en de keuze bewaard blijft bij een validatiefout.
- **Zoeken in het beheer** op winkelnaam via `?zoek=...`: hoofdletterongevoelig (`whereLike`, gebonden parameter), vindt ook verborgen winkels, met een melding en "Toon alle winkels" bij geen resultaten.
- De beheertabel toont per winkel "Zichtbaar"/"Verborgen" en "Uitgelicht".

### Toegevoegd in de derde stap (5 november 2026)

- **Eén foto of logo per winkel** (`stores.image_path`): optionele upload in het winkelformulier, alleen JPG/PNG/WebP tot 2 MB, op inhoud gecontroleerd aan de serverkant (`mimes`; SVG geweigerd). Laravel geeft het bestand een willekeurige naam in `storage/app/public/stores/`. In de database staat alleen het pad; `image_path` staat niet in `$fillable` en kan dus niet via het formulier worden meegestuurd.
  - Bij bewerken: huidige afbeelding zichtbaar, vervangen of "Afbeelding verwijderen". Een nieuwe upload gaat voor op verwijderen.
  - Bij een validatiefout blijft de oude afbeelding staan. Na succesvol vervangen of verwijderen, en bij het verwijderen van de winkel, wordt alleen het oude bestand van die winkel verwijderd (nooit iets buiten de map `stores/`).
  - Getoond op de winkelkaart (`/stores`) en bij uitgelichte winkels op de homepage, in het bestaande kaartvlak (`object-fit: contain`). Zonder afbeelding of bij een ontbrekend bestand: de standaardfoto van de website (`steen.png` op `/stores`, `primera.png` op de homepage).
- **Openingstijden** (`stores.opening_hours`, tekst tot 255 tekens): in het formulier en als gewone tekst op de winkelkaart; leeg = niets tonen.
- **Tellers** boven het winkeloverzicht: totaal, zichtbaar, verborgen. Ze tellen altijd alle winkels, ook tijdens zoeken.
- **Leesbaardere winkelkaarten** op `/stores`: kaarttekst van ±7 px naar 14–20 px, link onderstreept met zichtbare focusrand. Alleen de `.store-card`-regels in `stores.css` zijn aangepast.

### Opslag lokaal

- Uploads staan op de `public`-disk: `storage/app/public/stores/`. Die map en `public/storage` staan in `.gitignore`, dus uploads gaan niet mee in git.
- De website leest ze via `public/storage`. Eenmalig per checkout aanmaken: `php artisan storage:link`. Op Windows maakt Laravel daarvoor een directory-junction (`mklink /J`); daarvoor zijn geen beheerrechten of andere Windows-instellingen nodig.
- `APP_URL` in `.env` moet het adres zijn waarop je de site opent (hier `http://127.0.0.1:8123`), anders kloppen de afbeeldingsadressen niet.

### Samengevoegd met `main` (5 november 2026)

- Branch `bryan` (CMS) is samengevoegd in de nieuwste `main` (nieuwe homepage, menu, winkelpagina, agenda, plattegrond, te-huur- en bezoekpagina). Het uiterlijk van `main` is de basis; het CMS is daarin ingebouwd.
- Adressen: de winkelpagina heet nu `/stores` (zoals in het menu van `main`); `/winkels` stuurt met een 301 door, inclusief `?categorie=`. `/` en `/home` tonen allebei de homepage met de uitgelichte winkels.
- Menu en footer van `main`, aangevuld met "Naar beheer" / "Mijn account" voor ingelogde gebruikers en een link "Winkels" in de footer.
- Fallbackafbeeldingen komen uit `main` (`steen.png`, `primera.png`); een geüploade winkelafbeelding gaat voor. De leesbare kaarttekst uit het CMS is behouden.
- `import.meta.glob(['../assets/**'])` in `resources/js/app.js` zorgt dat ook de nieuwe afbeeldingen van `main` in de build zitten (anders geeft `Vite::asset()` een fout na `npm run build`).
- Gecontroleerd: `php artisan test` (71 geslaagd, 7 overgeslagen), `npm run build`, alle pagina's en media via HTTP, CMS-toevoegen/uploaden/verbergen/vervangen/verwijderen met een tijdelijke testwinkel (daarna verwijderd), schermafbeeldingen van homepage, winkelpagina, menu en beheer.

## Belangrijkste bestanden

| Bestand | Functie |
| --- | --- |
| `routes/web.php` | Homepage (`/` en `/home`, met uitgelichte winkels), `/stores` (+ doorverwijzing `/winkels`), overige pagina's, beheerroutes onder `/admin` |
| `routes/console.php` | Commando `admin:create` voor een beheeraccount |
| `app/Http/Controllers/Admin/StoreController.php` | CRUD, zoeken, tellers, upload/vervangen/verwijderen van de afbeelding, validatie |
| `app/Http/Middleware/EnsureUserIsAdmin.php` | Beheercontrole (alias `admin` in `bootstrap/app.php`) |
| `app/Models/Store.php` | Winkelmodel, categorielijst, casts, scope `visible()`, `imageUrl()` |
| `database/migrations/2026_11_04_*` | `is_admin` op users, tabel `stores` |
| `database/migrations/2026_11_05_000001_*` | `is_visible` en `is_featured` op `stores` |
| `database/migrations/2026_11_05_000002_*` | `image_path` en `opening_hours` op `stores` |
| `database/seeders/StoreSeeder.php` | 8 voorbeeldwinkels, waarvan 3 uitgelicht (herhaalbaar, overschrijft bestaande winkels niet) |
| `resources/views/admin/*` | Beheerlayout, overzicht met tellers en zoeken, formulier met checkboxes, upload en openingstijden |
| `resources/css/admin.css` | Stijl van het beheer (eigen Vite-entry) |
| `resources/views/stores.blade.php` + `resources/css/stores.css` | Openbare winkelpagina en winkelkaart (afbeelding, openingstijden, leesbare tekst) |
| `resources/views/home.blade.php` + `resources/css/home.css` | Blok "Uitgelichte Winkels" uit de database (met afbeelding; nette indeling bij 1 of 2 kaarten) |
| `tests/Feature/StoreManagementTest.php` | Featuretests voor winkelbeheer, zichtbaarheid, uitlichten, zoeken, afbeeldingen, openingstijden en tellers |

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
php artisan storage:link    # maakt public/storage voor de winkelafbeeldingen
npm run build
```

Starten: `php artisan serve --port=8123` en open `http://127.0.0.1:8123`.
Beheer: `http://127.0.0.1:8123/admin` (of via "Winkelier inloggen" in het menu).

Demo basis: inloggen → winkel bewerken of toevoegen → `/stores` vernieuwen → wijziging zichtbaar.

Demo zichtbaarheid en uitlichten:

1. Beheer → bij een winkel **Bewerken** → vink **Uitlichten op de homepage** aan → opslaan. In de tabel staat nu "Uitgelicht".
2. Open `/` en vernieuw: de winkel staat onder "Uitgelichte Winkels" (maximaal drie, alfabetisch).
3. Bewerk dezelfde winkel opnieuw → vink **Zichtbaar op de website** uit → opslaan. In de tabel staat "Verborgen".
4. Vernieuw `/` en `/stores`: de winkel is weg. In het beheer staat hij er nog; zoek bijvoorbeeld op een deel van de naam.

Demo afbeelding en openingstijden:

1. Beheer → **Bewerken** bij een winkel → kies bij **Foto of logo** een JPG, PNG of WebP (max. 2 MB) en vul **Openingstijden** in, bijv. "Ma–vr 09:00–18:00, za 09:00–17:00" → opslaan.
2. Vernieuw `/stores`: de kaart toont de afbeelding en de openingstijden. Is de winkel uitgelicht, dan staat de afbeelding ook op `/`.
3. Probeer een ander bestandstype (bijv. een SVG of een hernoemd tekstbestand): je krijgt een foutmelding en de oude afbeelding blijft staan.

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

## Uitgevoerde controles (derde stap, 5 november 2026)

- `php artisan test --filter=StoreManagementTest`: 33 geslaagd (13 nieuw). Volledige suite: 69 geslaagd, 7 overgeslagen (dezelfde Jetstream-API-tests). `npm run build` en `pint --test` slagen.
  - Tests gebruiken `Storage::fake('public')` en de in-memory testdatabase.
  - Gevonden tijdens het testen: `UploadedFile::fake()` bepaalt het type op basis van de bestandsnaam, waardoor een tekstbestand met de naam `nep.jpg` door de test kwam. Echte uploads worden op inhoud gecontroleerd. De tests gebruiken daarom echte tijdelijke bestanden (GD is niet geïnstalleerd, dus geen `fake()->image()`).
- Nieuwe migratie heen en terug getest op een losse database. `php artisan storage:link` uitgevoerd (junction `public/storage`).
- Via HTTP op de lokale server, met het bestaande beheeraccount en een tijdelijke winkel "TIJDELIJK Testwinkel (controle)" (daarna via het CMS verwijderd; uploadmap weer leeg; de 8 bestaande winkels ongewijzigd):
  - toevoegen met PNG → gegenereerde bestandsnaam, bereikbaar via `/storage/...`, zichtbaar op `/winkels`, `/` en `/home`;
  - bewerken zonder upload houdt de afbeelding; een hernoemd tekstbestand en een bestand van 3 MB geven een Nederlandse foutmelding en de oude afbeelding blijft;
  - vervangen, nieuwe upload + "verwijderen" tegelijk, expliciet verwijderen en winkel verwijderen ruimen alleen het eigen oude bestand op;
  - verbergen en weer zichtbaar maken op `/`, `/home`, `/winkels` en het categoriefilter; tellers 9/8/1, ook tijdens zoeken.
- Schermafbeeldingen (headless Chrome) van de winkelpagina (desktop, 500 px en 390 px), de homepage met uitgelichte afbeelding, het overzicht met tellers en het bewerkformulier met foutmelding.
- Toetsenbord (Chrome DevTools-protocol, echte Tab/Enter/spatie): op 390 px geen horizontale overloop en kaarttekst 14,4 px; link in de winkelkaart bereikbaar met zichtbare focusrand; inloggen alleen met het toetsenbord; uploadveld en "Afbeelding verwijderen" bereikbaar en met de spatiebalk te bedienen.

## Bewust nog niet in deze versie (vervolgwerk)

- Beheer van nieuws, evenementen/agenda, huurpanden en plattegrond.
- Winkeldetailpagina, categorieën als aparte tabel, paginering in het beheer, meerdere afbeeldingen of bijsnijden, gestructureerde openingstijden ("nu open").
- Uploads groter dan `post_max_size` (8 MB in de huidige `php.ini`) worden al door PHP geweigerd vóór Laravel; dan verschijnt een kale 413-foutpagina in plaats van een nette melding. Bestanden tussen 2 en 8 MB krijgen wel de Nederlandse foutmelding.
- Een gekozen bestand wordt bij een validatiefout niet onthouden; de beheerder moet het opnieuw kiezen (dat staat in de foutmelding).
- Bestaande navigatie: de links van het dichte mobiele menu zijn alleen buiten beeld geschoven en blijven onzichtbaar bereikbaar met Tab (ca. 17 extra Tab-stappen). Op de loginpagina staat "Wachtwoord vergeten?" tussen e-mail en wachtwoord in de tabvolgorde.
- De tekst op de homepagekaarten ("Uitgelichte Winkels") en de filterknoppen en kop op `/stores` zijn nog klein; alleen de winkelkaart is vergroot.
- Meerdere rollen (bijv. winkelier die alleen de eigen winkel beheert); nu alleen `is_admin`.
- Nederlandse vertaling van de Fortify/Jetstream-meldingen (bijv. foutmelding bij inloggen).
- Openbare pagina's die nog ontbreken: de footer linkt naar `/nieuws`, `/markten`, `/hamershofbon`, `/contact`, `/privacy` en `/cookies` (404).
- De plattegrond (`config/map-shops.php`) en de agenda gebruiken nog eigen vaste gegevens, los van het CMS.
- Bestaand ontwerp: lege `<img src="">` op de homepage, evenementen op de homepage en de categorietellingen ("18 winkels") zijn nog vaste voorbeeldtekst.
- Volgorde van uitgelichte winkels zelf kiezen (nu alfabetisch), en een waarschuwing in het beheer als meer dan drie winkels zijn uitgelicht.
- `db.sql` is alleen als inhoudelijke referentie gebruikt, niet geïmporteerd.
