# Contesto
Mac Solution Neo ha ormai consolidato un insieme stabile di regole su rapporto legacy/target, backend-first, governance dei bounded context, import legacy -> Neo e reportistica. Queste regole sono emerse in piu' task e vanno ora rese permanenti nei documenti root, per evitare di ripeterle in ogni prompt futuro.

# Obiettivo del task
Aggiornare in modo permanente `AGENTS.md` e `AI_CONTEXT.md`, e solo se utile `MASTER_PROGRESS.md`, per consolidare le regole ormai stabili senza aprire nuovi filoni implementativi o moltiplicare micro-step artificiali.

# Regole permanenti consolidate
- Legacy CI3 come baseline funzionale e operativa, non come baseline architetturale.
- Distinzione sempre obbligatoria tra:
  - comportamento legacy osservato;
  - modello target corretto;
  - strategia di transizione compatibile.
- Stack e direzione target permanenti:
  - Laravel + Vue
  - backend-first / API-first
  - mobile-ready
  - business logic nel backend, frontend come client.
- Bounded context progressivi e governabili:
  - step piccoli e verificabili;
  - ma non microscopici;
  - le micro-decisioni interne a uno stesso deliverable vanno assorbite nello step padre.
- Reportistica obbligatoria solo in `_codex_reports`, con timestamp completo all'inizio del nome file.
- Regola permanente legacy import -> Neo:
  - schema target;
  - mapping legacy -> target;
  - strategia di importazione o migrazione;
  - gap, collisioni, ambiguita' e chiavi di riconciliazione.

# File aggiornati
- `AGENTS.md`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# Cosa cambia nella gestione dei prossimi prompt
- Non sara' piu' necessario ribadire ogni volta:
  - che il legacy non va copiato ciecamente;
  - che la business logic va nel backend;
  - che il frontend Vue e' un client;
  - che ogni bounded context data-impact deve includere mapping/import legacy;
  - che i report devono stare solo in `_codex_reports`.
- I prossimi prompt potranno essere formulati a grana piu' umana, chiedendo macro-step o bounded context, senza dover riesplicitare l'intera cornice metodologica.

# Riduzione della proliferazione di micro-task
- E' stata fissata una regola esplicita: non ogni micro-ragionamento o rifinitura tecnica merita uno step autonomo.
- Un nuovo step si apre solo quando cambia davvero boundary, tipo di lavoro, deliverable o rischio.
- Le decisioni interne allo stesso macro-step devono essere documentate bene nei report, non trasformate in una catena dispersiva di micro-task.

# Regola permanente mapping/import legacy -> Neo
- E' stata resa permanente sia in `AGENTS.md` sia in `AI_CONTEXT.md`.
- Da ora ogni bounded context con impatto dati deve lasciare memoria stabile di:
  - schema target;
  - mapping dal legacy;
  - strategia di import o reimport;
  - gap e punti di riconciliazione.

# Limiti o punti da chiarire in futuro
- La regola di granularita' non elimina il bisogno di step piccoli: richiede solo che siano anche leggibili e utili.
- Restera' comunque necessario scegliere caso per caso il livello corretto di aggregazione dei prossimi macro-step.

# Raccomandazione operativa finale
I prossimi prompt possono ora concentrarsi direttamente sul prossimo macro-step utile del progetto, assumendo come gia' acquisite le regole consolidate di governance, rapporto legacy/target, backend-first e import legacy -> Neo.
