# File aggiornati
- `G:\Mirror\htdocs\macsolutionlaravel\AGENTS.md`
- `G:\Mirror\htdocs\macsolutionlaravel\AI_CONTEXT.md`

# Impatto runtime
- Impatto runtime nullo.
- Nessuna modifica a codice applicativo, migrazioni, seed, API o UI.
- Il cambiamento riguarda solo governance e contesto permanente del workspace.

# Come usare queste regole nei prossimi prompt
- Dare priorita' a macro-step leggibili, non a micro-task frammentati.
- Considerare il legacy come sorgente:
  - funzionale;
  - semantica;
  - dati.
- Quando il task tocca dati o persistenza, includere sempre:
  - schema target;
  - mapping legacy -> target;
  - strategia di import;
  - gap e ambiguita'.
- Quando il task dipende dal legacy, leggere non solo tabelle ma anche flussi finali, view e runtime.
- Leggere dry-run, commit gate e reconciliation come preparazione al big bang finale.

# Nota pratica per i prossimi prompt
- Non serve piu' ripetere ogni volta le regole di base su:
  - legacy vs target;
  - backend-first / mobile-ready;
  - flow semantics;
  - reportistica obbligatoria;
  - import legacy -> Neo.
- Serve invece concentrarsi sul bounded context e sul macro-step concreto da aprire.
