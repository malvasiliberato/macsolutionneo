# Workspace Governance Setup

## File introdotti
- `AGENTS.md`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`
- `_codex_reports/shared/architecture/20260402_173721_macsolution-neo_architecture_workspace-governance-bootstrap.md`
- `_codex_reports/infra/deploy/20260402_173721_macsolution-neo_deploy_workspace-governance-bootstrap.md`

## Cartelle create
- `_codex_reports/shared/architecture`
- `_codex_reports/infra/deploy`

## Precondizioni minime del workspace
- Workspace con root pulita e documentazione persistente accessibile in root.
- Presenza della cartella `_codex_reports` come unica sede dei report operativi.
- Adozione della convenzione Step ID `MSN-<AREA>-<NUMERO>`.
- Allineamento sul principio che il legacy CI3 e' baseline funzionale, non baseline architetturale.

## Note per repository e branching locale
- Mantenere i task piccoli e tracciabili anche a livello di branch locale.
- Preferire branch focalizzati per bounded context o per step di governance/audit/design.
- Evitare branch che mischiano piu' foundation e piu' moduli business insieme.
- Aggiornare la documentazione root e i report insieme ai task che chiariscono nuovo contesto o decisioni operative.

## Indicazioni operative minime per continuare i prossimi task senza disallineamenti
- Aprire ogni nuovo lavoro con uno step esplicito e stato iniziale dichiarato.
- Distinguere subito se il task e' di audit, design, implementazione, e2e o deploy.
- Se un task produce decisioni o avanzamento verificabile, creare o aggiornare il report corrispondente sotto `_codex_reports`.
- Aggiornare `MASTER_PROGRESS.md` quando cambia il focus o quando uno step passa a stato diverso.
- Non introdurre business logic finche' foundation e dipendenze minime non sono state chiarite.
- Nei task sul legacy, esplicitare sempre comportamento osservato, target corretto e strategia di transizione.
