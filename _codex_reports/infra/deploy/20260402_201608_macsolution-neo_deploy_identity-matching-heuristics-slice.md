# Identity Matching Heuristics Slice Deploy Notes

## File creati o aggiornati
- `app/Application/Auth/LegacyImport/DefaultAuthOrgLegacyMatchingHeuristics.php`
- `app/Application/Auth/LegacyImport/DefaultAuthOrgLegacyCandidateMatcher.php`
- `app/Application/Auth/LegacyImport/AuthOrgLegacyDryRun.php`
- `app/Application/Auth/LegacyImport/MySqlAuthOrgLegacyDatasetAdapter.php`
- `tests/Feature/Console/AuthOrgLegacyImportCommandTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Impatto runtime
- Il comando `legacy:import:auth-org --dry-run` usa ora euristiche concrete di matching.
- Nessuna route, API o migration modificata.
- Nessuna scrittura introdotta.

## Verifica consigliata
- `php artisan legacy:import:auth-org --dry-run`
- `php artisan test`
