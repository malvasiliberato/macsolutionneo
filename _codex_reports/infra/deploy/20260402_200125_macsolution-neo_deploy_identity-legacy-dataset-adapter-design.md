# Identity Legacy Dataset Adapter Design Deploy Notes

## File creati o aggiornati
- `app/Application/Auth/LegacyImport/Contracts/AuthOrgLegacyDatasetAdapter.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Impatto runtime
- Nullo.
- Nessuna modifica al comando attuale.
- Nessuna migration, route o API modificata.

## Uso operativo
- Questo step definisce il contratto del prossimo adapter.
- Il comando `legacy:import:auth-org --dry-run` continua a usare il dataset controllato attuale.
- Il prossimo slice corretto e' implementare un adapter read-only dietro il contratto appena introdotto.
