# Identity Legacy Import Dry-Run Slice Deploy Notes

## File creati o aggiornati
- `app/Application/Auth/LegacyImport/AuthOrgLegacyDryRun.php`
- `app/Console/Commands/LegacyImport/AuthOrgLegacyImportCommand.php`
- `tests/Feature/Console/AuthOrgLegacyImportCommandTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Impatto runtime
- Introdotto un solo comando console read-only.
- Nessuna route, API, migration o UI modificata.
- Nessuna scrittura effettuata in modalita' `--dry-run`.

## Verifica consigliata
- eseguire `php artisan legacy:import:auth-org --dry-run`
- eseguire `php artisan test`

## Uso operativo
- Usare questo comando solo come dry-run tecnico.
- Non usarlo ancora come import reale.
- Mantenere il dataset controllato finche' non sara' validato l'adapter legacy reale.
