# File aggiornati
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Impatto operativo
- Impatto runtime nullo
- Nessuna migrazione
- Nessun seed
- Nessun comando applicativo modificato

## Come usare questa base nei prossimi task
- Usare `supporto` come primo `manual_link_candidate` del lane umano/admin
- Trattare `admin` e `rosy` come `manual_review_only`
- Non riportare `bo_*` nel lane umano
- Non riaprire il caso `mac` dentro il lane umano

## Nota pratica
Nei prossimi prompt e' sufficiente riferirsi a:
- short-list umana `dealer admin`
- `supporto` come primo candidato utile
- `admin` e `rosy` come review-only

Questo evita di rieseguire ogni volta il censimento completo dei principal `dealer.tipo=admin`.
