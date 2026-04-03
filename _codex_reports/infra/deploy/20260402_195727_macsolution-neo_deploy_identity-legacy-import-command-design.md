# Identity Legacy Import Command Design Deploy Notes

## File aggiornati
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## File creati
- `_codex_reports/shared/architecture/20260402_195727_macsolution-neo_architecture_identity-legacy-import-command-design.md`
- `_codex_reports/infra/deploy/20260402_195727_macsolution-neo_deploy_identity-legacy-import-command-design.md`

## Impatto runtime
- Nullo.
- Nessun comando reale e' stato ancora introdotto.
- Nessuna migration, route o API modificata.

## Uso operativo
- Questo step fissa il contratto prima dell'implementazione.
- Il prossimo slice corretto e' un comando `dry-run` ristretto e verificabile.
- Evitare di collegare subito il comando a tutto il DB legacy o a piu' bounded context insieme.
