**EERST EXTENSIES DOWNLOADEN**
Laravel blade snippets
Laravel Snippets
Laravel Artisan
Laravel Extra intellisense
Laravel Blade formatter
Laravel goto view

**Stappen die jullie moeten volgen (nada de code is gepulled):**

bash
composer install

Installeert alle PHP-dependencies.

bash
cp .env.example .env
php artisan key:generate

Maakt hun eigen lokale .env bestand aan met een unieke app-key. (Let op: .env hoort normaal niet meegecommit te worden in git — check of dat ook zo is in jullie repo.)

bash
touch database/database.sqlite
php artisan migrate

Maakt de lokale SQLite-database aan en zet de tabellen erin.

bash
npm install
npm run dev

Installeert de frontend-dependencies en start de Vite dev-server (nodig voor Inertia/Vue of React).

bash
php artisan serve

Start de Laravel-server zelf. (In een aparte terminal naast npm run dev.)

**hopelijk werkt het gelijk**