# Identity Legacy Dataset Adapter Slice Deploy Notes

## File creati o aggiornati
- `app/Application/Auth/LegacyImport/MySqlAuthOrgLegacyDatasetAdapter.php`
- `app/Application/Auth/LegacyImport/AuthOrgLegacyDryRun.php`
- `config/legacy_import.php`
- `config/database.php`
- `.env.example`
- `.env.testing`
- `tests/Feature/Console/AuthOrgLegacyImportCommandTest.php`

## Impatto runtime
- Il comando `legacy:import:auth-org --dry-run` puo' ora usare un adapter legacy reale read-only se configurato.
- Nessuna route o API modificata.
- Nessuna scrittura su target o mapping.

## Note operative Laragon
- configurare la connessione legacy tramite:
  - `LEGACY_IMPORT_DB_CONNECTION`
  - `LEGACY_IMPORT_DB_HOST`
  - `LEGACY_IMPORT_DB_PORT`
  - `LEGACY_IMPORT_DB_DATABASE`
  - `LEGACY_IMPORT_DB_USERNAME`
  - `LEGACY_IMPORT_DB_PASSWORD`
- tabelle auth/org configurabili:
  - `LEGACY_IMPORT_AUTH_ORG_DEALER_TABLE`
  - `LEGACY_IMPORT_AUTH_ORG_MEMBERSHIP_TABLE`

## Verifica consigliata
- `php artisan legacy:import:auth-org --dry-run`
- `php artisan legacy:import:auth-org --dry-run --dataset=legacy-ci3-auth-org`
- `php artisan test`
