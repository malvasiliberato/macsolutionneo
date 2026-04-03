# Identity Candidate Resolution Design Deploy Notes

## File creati o aggiornati
- `app/Application/Auth/LegacyImport/Contracts/AuthOrgLegacyCandidateResolver.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Impatto runtime
- Nullo.
- Nessuna modifica al comando o all'adapter in questo step.

## Uso operativo
- Questo step definisce il contratto del resolver.
- Il prossimo slice corretto e' implementare il resolver mantenendo il dry-run read-only.
