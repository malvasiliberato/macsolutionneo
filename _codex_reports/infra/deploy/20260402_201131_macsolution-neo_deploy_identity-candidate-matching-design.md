# Identity Candidate Matching Design Deploy Notes

## File creati o aggiornati
- `app/Application/Auth/LegacyImport/Contracts/AuthOrgLegacyCandidateMatcher.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Impatto runtime
- Nullo.
- Nessuna modifica a comando, adapter o resolver in questo step.

## Uso operativo
- Questo step fissa il contratto del matcher.
- Il prossimo slice corretto e' implementare il matcher mantenendo il workflow read-only.
