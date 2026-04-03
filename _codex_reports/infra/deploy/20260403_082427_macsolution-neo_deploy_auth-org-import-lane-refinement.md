# File aggiornati
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Impatto operativo
- nullo sul runtime
- nessuna migration
- nessun seed
- nessun comando modificato

## Come usare questa base nei prossimi task
- quando il prompt riguarda import/reconciliation AUTH/ORG, partire dai lane:
  - `internal_platform_principal`
  - `dealer_organization`
  - `dealer_seller`
  - `dealer_operator`
- evitare richieste o implementazioni che leggano direttamente:
  - `dealer`
  - `dealer_collaboratore`
  - `commerciali`
  come target model impliciti

## Nota pratica
Il prossimo step puo' restare ancora prudente e non distruttivo:
- aggiornare candidate classification e reporting per lane
- solo dopo valutare se servono nuovi commit o review flow mirati
