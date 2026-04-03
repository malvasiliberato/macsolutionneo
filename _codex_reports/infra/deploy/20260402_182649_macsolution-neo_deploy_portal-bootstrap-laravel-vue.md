# Portal Bootstrap Setup

## Prerequisiti locali
- Windows con Laragon
- PHP 8.1+
- Composer 2+
- Node 20+
- MySQL 8+

## Comandi usati per bootstrap e verifica
```powershell
composer create-project laravel/laravel temp_portal_bootstrap
composer require laravel/breeze --dev
php artisan breeze:install vue
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS macsolution_neo ..."
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS macsolution_neo_testing ..."
mysql -uroot -e "CREATE USER IF NOT EXISTS 'macsolution_neo'@'localhost' IDENTIFIED BY 'macsolution_neo'; ..."
php artisan key:generate --env=testing
php artisan migrate:fresh --seed
php artisan test
npm.cmd run build
```

## Struttura file/cartelle rilevante
- `app/`
- `config/`
- `database/`
- `public/build/`
- `public/vendor/velzon/assets/`
- `resources/js/`
- `routes/`
- `tests/`
- `AGENTS.md`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`
- `README.md`

## File introdotti o aggiornati in modo rilevante
- bootstrap Laravel/Vue completo in root
- foundation auth/API/web/database
- asset Velzon sotto `public/vendor/velzon/assets`
- reportistica sotto `_codex_reports`
- documentazione operativa e governance persistente

## Come avviare il progetto in locale con Laragon
```powershell
composer install
npm.cmd install
php artisan migrate --seed
```

Poi:
- avviare Apache e MySQL da Laragon
- aprire `http://macsolutionlaravel.loc/`

Per sviluppo frontend con watcher:
```powershell
npm.cmd run dev
```

## Env e config necessari
- `.env` usa:
  - `APP_URL=http://macsolutionlaravel.loc`
  - `DB_HOST=localhost`
  - `DB_DATABASE=macsolution_neo`
  - `DB_USERNAME=macsolution_neo`
  - `DB_PASSWORD=macsolution_neo`
- `.env.testing` usa:
  - `DB_DATABASE=macsolution_neo_testing`
  - `DB_USERNAME=macsolution_neo`
  - `DB_PASSWORD=macsolution_neo`
- `SANCTUM_STATEFUL_DOMAINS` include `macsolutionlaravel.loc`

## Note DB locale
- Database richiesti:
  - `macsolution_neo`
  - `macsolution_neo_testing`
- Utente locale creato per il workspace:
  - `macsolution_neo@localhost`
- Seed bootstrap:
  - utente `admin@macsolution.test`
  - ruolo `platform_admin`

## Come verificare che il bootstrap sia riuscito
- `php artisan test` deve passare
- `npm.cmd run build` deve completare senza errori
- `php artisan route:list` deve mostrare `dashboard`, `workspace.roadmap`, `api.v1.health`, `api.v1.me`
- login con:
  - email `admin@macsolution.test`
  - password `password`

## Indicazioni operative minime per i prossimi task
- Evolvere il progetto direttamente dalla base root attuale, non dal folder temporaneo di bootstrap.
- Aprire il prossimo step su `Identity + Organization` senza introdurre ancora moduli business finali.
- Continuare a usare Velzon solo come riferimento visuale della shell, non come struttura applicativa o sorgente di business logic.
- Considerare Laragon come ambiente locale standard nelle istruzioni operative successive.
