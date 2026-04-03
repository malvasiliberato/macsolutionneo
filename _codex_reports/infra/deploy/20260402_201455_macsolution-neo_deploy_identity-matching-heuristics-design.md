# Identity Matching Heuristics Design Deploy Notes

## File creati o aggiornati
- `app/Application/Auth/LegacyImport/Contracts/AuthOrgLegacyMatchingHeuristics.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Impatto runtime
- Nullo.
- Nessuna modifica a matcher, resolver, command o migration in questo step.

## Uso operativo
- Questo step fissa le euristiche da implementare nel prossimo slice.
- Il prossimo step corretto e' introdurre euristiche concrete restando ancora read-only.
