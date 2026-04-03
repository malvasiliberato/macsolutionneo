# File creati o aggiornati
- `app/Application/Auth/LegacyImport/DefaultAuthOrgLegacyMatchingConfidence.php`
- `app/Application/Auth/LegacyImport/DefaultAuthOrgLegacyCandidateMatcher.php`
- `app/Application/Auth/LegacyImport/AuthOrgLegacyDryRun.php`
- `tests/Feature/Console/AuthOrgLegacyImportCommandTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# Impatto runtime
- Minimo e confinato al dry-run auth/org.
- Nessuna route, migration, seeder o commit mode introdotti.

# Come verificare
- Eseguire:
  - `php artisan legacy:import:auth-org --dry-run`
  - `php artisan legacy:import:auth-org --dry-run --dataset=bootstrap-auth-org-edge-cases`
  - `php artisan test --filter=AuthOrgLegacyImportCommandTest`
- Verificare la presenza delle nuove metriche di confidence nell'output.

# Note Laragon
- Nessuna modifica specifica a host o database locale.
- Il comportamento resta compatibile con Windows + Laragon.
