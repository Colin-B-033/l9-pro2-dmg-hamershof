# De Hamershof – Laravel-project

Werkt gewoon op Windows (WSL is **niet** nodig). Je hebt nodig: PHP 8.2+, Composer en Node.js.
Het project gebruikt **SQLite**: de database is één bestand (`database/database.sqlite`), dus je hoeft geen MySQL/MariaDB te installeren.

## Eerst extensies downloaden (VS Code)

- Laravel Blade Snippets
- Laravel Snippets
- Laravel Artisan
- Laravel Extra Intellisense
- Laravel Blade Formatter
- Laravel goto view

## Eerste keer installeren

Voer dit uit in de projectmap:

```bash
composer install
npm install
cp .env.example .env              # Windows PowerShell: copy .env.example .env
php artisan key:generate
php -r "touch('database/database.sqlite');"
php artisan migrate
php artisan db:seed --class=StoreSeeder
php artisan storage:link
npm run build
```

- `migrate` maakt de tabellen aan (o.a. `stores` voor het CMS).
- `db:seed --class=StoreSeeder` zet 8 voorbeeldwinkels in de database (mag je overslaan).
- `storage:link` zorgt dat geüploade winkelafbeeldingen zichtbaar zijn.

## Starten

```bash
php artisan serve
```

Open daarna http://127.0.0.1:8000. Werk je aan de opmaak, start dan in een tweede terminal `npm run dev` (dan zie je wijzigingen direct).

## Na elke `git pull`

```bash
composer install
npm install
php artisan migrate
npm run build
```

## Beheer (CMS)

Maak je eigen beheeraccount aan:

```bash
php artisan admin:create jouw@email.nl --name="Jouw naam"
```

Het wachtwoord wordt één keer in de terminal getoond. Inloggen via `/login`; daarna kom je in het beheer (`/admin/winkels`).
Iedereen heeft een eigen lokale database: winkels en accounts van een teamgenoot zie je niet bij jou.

## Problemen oplossen

### `Access denied for user 'root'@'localhost'` (Connection: mysql/mariadb) – volledige handleiding

**Wat is het probleem?**
Je `.env` staat nog op MySQL/MariaDB (`DB_CONNECTION=mariadb` of `mysql`). Laravel probeert in te loggen op een databaseserver die niet draait of een ander wachtwoord heeft. Sinds het CMS haalt de homepage winkels uit de database, dus zie je deze fout meteen op `/` en `/home`.
De oplossing: het project op **SQLite** zetten, net als de rest van het team.

**Stap 1 – Stop de server**
Ga naar de terminal waarin `php artisan serve` draait en druk op `Ctrl + C`.

**Stap 2 – Pas `.env` aan**
Open het bestand `.env` in de hoofdmap van het project (in VS Code: links in de bestandslijst). Zoek het blok met regels die beginnen met `DB_`, bijvoorbeeld:

```
DB_CONNECTION=mariadb
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=rocglas_hf
DB_USERNAME=root
DB_PASSWORD=
```

Vervang dat hele blok door:

```
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

Let op: `DB_DATABASE` moet uitgezet zijn (met `#`), anders zoekt Laravel naar een verkeerd bestand. Sla het bestand op.

**Stap 3 – Maak het databasebestand aan**
Voer in de terminal, in de projectmap, uit:

```bash
php -r "touch('database/database.sqlite');"
```

Dit maakt een leeg bestand `database/database.sqlite`. (Bestaat het al, dan gebeurt er niets.)

**Stap 4 – Laat Laravel de nieuwe instellingen lezen**

```bash
php artisan config:clear
```

Controle: `php artisan config:show database.default` moet `sqlite` tonen.

**Stap 5 – Maak de tabellen en voorbeeldwinkels aan**

```bash
php artisan migrate
php artisan db:seed --class=StoreSeeder
```

Vraagt `migrate` of het bestand aangemaakt moet worden, antwoord dan `yes`.

**Stap 6 – Frontend en afbeeldingen**

```bash
npm install
npm run build
php artisan storage:link
```

**Stap 7 – Starten en testen**

```bash
php artisan serve
```

Open http://127.0.0.1:8000/home. Je ziet de homepage; op `/stores` staan de voorbeeldwinkels.

**Stap 8 (optioneel) – Beheeraccount**

```bash
php artisan admin:create jouw@email.nl --name="Jouw naam"
```

Het wachtwoord verschijnt één keer in de terminal. Log in via `/login`.

**Nog steeds een fout?**

- `could not find driver` → de SQLite-extensie van PHP staat uit. Open je `php.ini` (pad zie je met `php --ini`), zoek `;extension=pdo_sqlite` en `;extension=sqlite3`, haal de `;` weg, sla op en start de terminal opnieuw.
- Nog steeds `Connection: mariadb` in de fout → `.env` niet opgeslagen, of `config:clear` niet uitgevoerd. Controleer met `php artisan config:show database.default`.
- `Database file at path [...] does not exist` → stap 3 opnieuw doen en controleren dat `DB_DATABASE` uitgezet is.
- Andere fout → kopieer de bovenste regels van de rode foutpagina en stuur die naar het team.

### `no such table: stores` (of een andere tabel)

```bash
php artisan migrate
```

### `Database file at path [...] does not exist`

```bash
php -r "touch('database/database.sqlite');"
php artisan migrate
```

### `Unable to locate file in Vite manifest`

```bash
npm install
npm run build
```

### Afbeeldingen van winkels laden niet

```bash
php artisan storage:link
```

Controleer ook dat `APP_URL` in `.env` het adres is waarop je de site opent (bijv. `http://127.0.0.1:8000`).

### Wijziging in `.env` lijkt niets te doen

```bash
php artisan config:clear
```

Stop daarna de server (Ctrl+C) en start opnieuw met `php artisan serve`.

> Zet nooit je `.env` of `database/database.sqlite` in git; die zijn lokaal en staan in `.gitignore`.
