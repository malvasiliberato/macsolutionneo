# File aggiornati
- `app/Application/Auth/LegacyImport/DefaultAuthOrgLegacyCandidateResolver.php`
- `app/Application/Auth/LegacyImport/AuthOrgLegacyReconciliationReportBuilder.php`
- `tests/Feature/Console/AuthOrgLegacyImportCommandTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Impatto runtime
- nullo sul portale
- nessuna migrazione
- nessun seed
- nessuna modifica al gate `create-only`

## Verifica locale
- eseguire:
  - `php artisan test --filter=AuthOrgLegacyImportCommandTest`
- il dry-run AUTH/ORG ora espone anche:
  - `lane_breakdown`

## Come usare questo slice
- nei prossimi task leggere prima il lane semantico del candidato
- usare il lane `unknown` come backlog tecnico esplicito, non come scorciatoia da nascondere
