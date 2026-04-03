# Workspace Governance Bootstrap

## Contesto
Nuovo workspace Mac Solution Neo destinato a sostituire progressivamente Mac Solution legacy (CodeIgniter 3), con focus in questo task sulla sola impalcatura di governance e non sull'implementazione di moduli business.

## Obiettivo del task
Introdurre una governance minima ma solida, riusabile nei prossimi prompt, capace di guidare audit, design, implementazione, e2e e deploy con metodo coerente, reportistica obbligatoria e distinzione chiara tra legacy osservato e target architetturale.

## File creati o aggiornati
- `AGENTS.md`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`
- `_codex_reports/shared/architecture/20260402_173721_macsolution-neo_architecture_workspace-governance-bootstrap.md`
- `_codex_reports/infra/deploy/20260402_173721_macsolution-neo_deploy_workspace-governance-bootstrap.md`

## Struttura di governance introdotta
- `AGENTS.md` definisce regole operative permanenti, convenzione step, stati, tipi di lavoro, reportistica e done condition.
- `AI_CONTEXT.md` conserva il contesto stabile del progetto e i principi da non perdere nei task successivi.
- `MASTER_PROGRESS.md` agisce come plancia sintetica con filoni principali, step iniziali e dipendenze logiche.
- `_codex_reports/...` centralizza la reportistica operativa del bootstrap.

## Convenzioni adottate
- Step ID: `MSN-<AREA>-<NUMERO>`
- Aree predisposte: `GOV`, `AUD`, `AUTH`, `ORG`, `CAT`, `DEAL`, `UI`, `API`, `DOC`, `NOTIF`, `FIN`, `SIGN`, `E2E`, `DEP`, `SHARED`
- Stati ammessi: `backlog`, `ready`, `in_corso`, `bloccato`, `validato`, `chiuso`, `no_go`
- Tipi di lavoro separati: `audit`, `design`, `implementazione`, `e2e`, `deploy`, `explain-only`
- Report con timestamp iniziale obbligatorio e collocazione esclusiva sotto `_codex_reports`

## Motivazione delle scelte
- La documentazione root mantiene pochi artefatti stabili e facili da consultare.
- La convenzione step e' semplice, leggibile e scalabile su bounded context multipli.
- La distinzione tra audit, design e implementazione riduce il rischio di saltare direttamente in codice senza chiarire fondazioni e transizione.
- L'accento su backend-first / API-first / mobile-ready evita di costruire fondazioni dipendenti da una singola UI.
- La separazione esplicita tra legacy behavior, target domain e transition strategy protegge da copie architetturali improprie del CI3.

## Come usare AGENTS, AI_CONTEXT e MASTER_PROGRESS
- Usare `AGENTS.md` come regola esecutiva di ogni nuovo task.
- Usare `AI_CONTEXT.md` come memoria persistente del progetto e dei principi gia' chiariti.
- Usare `MASTER_PROGRESS.md` come plancia sintetica da aggiornare quando uno step cambia stato o quando emerge un nuovo filone rilevante.

## Primi step suggeriti da aprire
- `MSN-AUD-001` audit legacy identity and organization
- `MSN-AUTH-001` identity foundation target model
- `MSN-ORG-001` organization foundation target model
- `MSN-CAT-001` audit legacy catalog foundation
- `MSN-SHARED-001` capability and sidebar governance

## Dubbi o limiti del bootstrap
- Il workspace non contiene ancora audit consolidati da cui derivare dettagli piu' specifici.
- Alcune note su errori legacy e gerarchie di catalogo sono quindi formulate in modo prudente e dichiaratamente non definitive.
- Questo bootstrap non introduce backlog completo dei moduli, per evitare widening prematuro.
