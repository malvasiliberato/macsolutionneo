# Mac Solution Neo

Bootstrap reale del nuovo portale Laravel + Vue destinato a sostituire progressivamente Mac Solution legacy (CodeIgniter 3).

## Stack
- Laravel 10
- Vue 3 con Inertia
- Sanctum per auth web/API foundation
- MySQL locale
- Velzon come base visuale della shell UI

## Prerequisiti locali
- PHP 8.1+
- Composer 2+
- Node 20+
- MySQL 8+
- Laragon su Windows come ambiente locale di riferimento

## Setup rapido su Laragon
```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS macsolution_neo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -uroot -e "CREATE USER IF NOT EXISTS 'macsolution_neo'@'localhost' IDENTIFIED BY 'macsolution_neo'; GRANT ALL PRIVILEGES ON macsolution_neo.* TO 'macsolution_neo'@'localhost'; FLUSH PRIVILEGES;"
php artisan migrate --seed
npm.cmd install
npm.cmd run build
```

## Host locale Laragon consigliato
- Posizionare il progetto sotto la root siti di Laragon e usare l'host automatico del workspace, in questo caso `http://macsolutionlaravel.loc/`
- Valori coerenti nel file `.env`:
  - `APP_URL=http://macsolutionlaravel.loc`
  - `DB_HOST=localhost`
  - `DB_DATABASE=macsolution_neo`
  - `DB_USERNAME=macsolution_neo`
  - `DB_PASSWORD=macsolution_neo`

## Ambiente di test
```powershell
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS macsolution_neo_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -uroot -e "GRANT ALL PRIVILEGES ON macsolution_neo_testing.* TO 'macsolution_neo'@'localhost'; FLUSH PRIVILEGES;"
php artisan test
```

## Avvio locale su Laragon
- Avviare Apache e MySQL da Laragon
- Aprire il portale su `http://macsolutionlaravel.loc/`
- Per sviluppo frontend con Vite: `npm.cmd run dev`
- Per build asset: `npm.cmd run build`
- `php artisan serve` resta opzionale solo come fallback tecnico, non come flusso locale principale

## Credenziali bootstrap locali
- Email: `admin@macsolution.test`
- Password: `password`

## Note architetturali
- La shell web e' autenticata e resta volutamente sobria.
- La shell UI riusa in modo selettivo asset e pattern Velzon presenti localmente in `H:\Velzon_v2.2.0`.
- La business logic resta nel backend.
- Le API partono da `/api/v1`.
- Ruoli e capability sono solo una foundation tecnica, non il modello ACL finale di dominio.
- Organization, catalog e moduli business restano volutamente rimandati ai prossimi step.
- Il setup locale di riferimento e' Laragon; non sono richiesti Docker, Sail o Valet in questo bootstrap.

## Git workspace
Bootstrap minimo consigliato del repository locale:

```powershell
git init
git add .
git commit -m "chore: bootstrap macsolution neo workspace"
```

Note:
- `.env`, `vendor`, `node_modules`, cache e artefatti runtime non devono essere versionati.
- `_codex_reports` resta parte del workspace e puo' essere versionato per mantenere audit e governance.

## Plesk auto deploy
Il workspace include uno script prudente per deploy automatico Plesk:

- script: [scripts/deploy/plesk/post-deploy.sh](G:\Mirror\htdocs\macsolutionlaravel\scripts\deploy\plesk\post-deploy.sh)

Uso consigliato in Plesk Git deployment:
```bash
bash scripts/deploy/plesk/post-deploy.sh
```

Variabili opzionali supportate dallo script:
- `PHP_BIN`: binario PHP da usare su Plesk
- `COMPOSER_BIN`: binario Composer da usare su Plesk
- `RUN_MIGRATIONS=1`: abilita `php artisan migrate --force`
- `RUN_FRONTEND_BUILD=1`: abilita `npm ci && npm run build` solo se Node e npm sono disponibili sul server

Comportamento dello script:
- esegue `composer install --no-dev`
- pulisce e ricostruisce le cache Laravel
- crea `public/storage` se manca
- esegue migrazioni solo se esplicitamente abilitate
- esegue build frontend solo se esplicitamente abilitata
- riavvia le queue in modo non bloccante

Scelta prudenziale:
- di default lo script non lancia ne' migrazioni ne' build frontend
- questo evita deploy aggressivi o dipendenti da tool non presenti su Plesk

Checklist minima Plesk:
- repository Git collegato al document root corretto
- `.env` gia' presente sul server
- `storage` e `bootstrap/cache` scrivibili
- PHP CLI e Composer disponibili
- Node/npm opzionali, solo se si vuole buildare sul server
