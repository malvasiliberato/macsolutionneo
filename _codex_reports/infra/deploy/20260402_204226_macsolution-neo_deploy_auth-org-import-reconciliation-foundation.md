# File creati o modificati
- `app/Application/Auth/LegacyImport/AuthOrgLegacyReconciliationReportBuilder.php`
- `app/Application/Auth/LegacyImport/AuthOrgLegacyDryRun.php`
- `app/Application/Auth/LegacyImport/DefaultAuthOrgLegacyCandidateMatcher.php`
- `app/Application/Auth/LegacyImport/DefaultAuthOrgLegacyCandidateResolver.php`
- `app/Application/Auth/LegacyImport/DefaultAuthOrgLegacyMatchingConfidence.php`
- `app/Application/Auth/LegacyImport/DefaultAuthOrgLegacyMatchingHeuristics.php`
- `app/Application/Auth/LegacyImport/MySqlAuthOrgLegacyDatasetAdapter.php`
- `app/Application/Auth/LegacyImport/Contracts/AuthOrgLegacyMatchingHeuristics.php`
- `app/Console/Commands/LegacyImport/AuthOrgLegacyImportCommand.php`
- `tests/Feature/Console/AuthOrgLegacyImportCommandTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# Migrazioni introdotte
- Nessuna nuova migration in questo macro-step.
- La foundation usa la migration gia' esistente di `legacy_entity_mappings`.

# Comandi o service introdotti
- Service:
  - `AuthOrgLegacyReconciliationReportBuilder`
- Command aggiornato:
  - `php artisan legacy:import:auth-org --dry-run`

# Impatto runtime
- Limitato al filone auth/org legacy import.
- Nessun commit mode attivato.
- Nessuna scrittura distruttiva sul dominio finale.

# Come verificare localmente
- Eseguire:
  - `php artisan legacy:import:auth-org --dry-run`
  - `php artisan legacy:import:auth-org --dry-run --dataset=bootstrap-auth-org-edge-cases`
  - `php artisan test`
- Verificare che il comando mostri:
  - summary batch
  - reconciliation snapshot
  - `commit_mode=not_available`

# Note Laragon/local setup
- Nessuna nota speciale oltre al setup Laragon gia' in uso.
- La suite completa `php artisan test` resta il riferimento piu' affidabile anche per questo macro-step.
