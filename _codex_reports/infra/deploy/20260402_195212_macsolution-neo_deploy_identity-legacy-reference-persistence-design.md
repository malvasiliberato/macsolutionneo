# Identity Legacy Reference Persistence Design Deploy Notes

## File aggiornati
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## File creati
- `_codex_reports/shared/architecture/20260402_195212_macsolution-neo_architecture_identity-legacy-reference-persistence-design.md`
- `_codex_reports/infra/deploy/20260402_195212_macsolution-neo_deploy_identity-legacy-reference-persistence-design.md`

## Impatto sul runtime applicativo
- Nullo.
- Nessuna migration, route, model o seed modificata in questo step.

## Risultato operativo
- Il workspace ora ha una regola concreta per scegliere tra:
  - `legacy_id` diretto
  - mapping table tecnica
  - tracking tecnico esterno al dominio

## Come usare questo step nei prossimi task
- Prima di introdurre campi `legacy_*` su un aggregate, verificare se il mapping e' davvero uno-a-uno e stabile.
- Se il mapping e' ambiguo o multi-sorgente, preferire una mapping table tecnica.
- Per auth/org, il prossimo step corretto e' implementare la persistence tecnica minima, non l'import completo.
