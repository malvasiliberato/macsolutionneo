# AGENTS.md

## Scopo del progetto
Questo workspace governa la costruzione progressiva della nuova piattaforma Mac Solution, destinata a sostituire in modo graduale il legacy basato su CodeIgniter 3. Lo scopo di questo file e' definire regole operative stabili, riusabili e verificabili per tutti i task futuri.

## Stack target
- Backend: Laravel
- Frontend: Vue
- Approccio: backend-first, API-first
- Obiettivo evolutivo: readiness per futura app mobile
- Base visuale shell UI: Velzon, riusato solo per layout, asset e pattern utili
- Ambiente locale di riferimento: Windows + Laragon

## Principi architetturali
- Il legacy CI3 e' baseline funzionale e operativa da esplorare, non baseline architetturale da copiare.
- Ogni decisione deve distinguere esplicitamente tra comportamento legacy, modello di dominio target corretto, strategia di transizione.
- L'evoluzione deve privilegiare minimum migration risk e maximum architectural clarity.
- Il backend e il contratto applicativo/API vengono definiti prima di eventuali dettagli UI.
- Le scelte devono restare compatibili con futura esposizione mobile.
- I bounded context vanno trattati come sezioni progressive e verificabili.
- Velzon puo' essere usato come base visuale della shell, non come base di business logic, non come struttura architetturale del progetto e non come sorgente da copiare ciecamente.
- La migrazione finale attesa e' di tipo big bang, ma deve essere preparata in modo controllato tramite audit, mapping, reconciliation, dry-run, commit gate e import idempotenti.

## Regole permanenti di esecuzione task
- Nessun widening non richiesto: ogni task resta nel perimetro esplicitamente chiesto.
- Nessuna business logic di modulo viene introdotta se il task e' solo di audit, design o governance.
- Audit, design, implementazione, e2e, deploy ed explain-only vanno sempre distinti.
- Ogni task deve dichiarare input, output, perimetro e done condition.
- I task vanno formulati e gestiti per bounded context o macro-step leggibili, non per singola micro-decisione isolata.
- Step piccoli e verificabili non significa step microscopici: se una decisione e' interna a uno step padre e non apre un vero boundary nuovo, va assorbita nello step esistente.
- Non trasformare automaticamente ogni rifinitura tecnica, naming interno o micro-variante di ragionamento in uno step autonomo del master progress.
- Ogni bounded context con impatto dati deve lasciare traccia esplicita di:
  - schema target;
  - mapping legacy -> target;
  - strategia di importazione o migrazione dati;
  - gap, collisioni, ambiguita' e record non mappabili automaticamente;
  - eventuali chiavi di riconciliazione o legacy references utili.
- I file dichiarati come creati o modificati vanno realmente verificati sul filesystem prima della chiusura del task.
- I report devono vivere solo sotto `_codex_reports`.
- Nessun report deve essere creato fuori da `_codex_reports`.
- Il naming dei report deve iniziare sempre con timestamp completo `YYYYMMDD_HHMMSS`.
- Il master progress deve restare aggiornabile e sintetico.
- I documenti di contesto persistente vanno mantenuti in root, non dispersi.
- Il bootstrap tecnico reale del portale va preservato e fatto evolvere per step, evitando reset o riscritture caotiche della base Laravel + Vue esistente.

## Regola permanente legacy vs target
- Ogni bounded context deve distinguere in modo esplicito e verificabile tra:
  - comportamento legacy reale;
  - modello target corretto;
  - strategia di transizione compatibile.
- Il legacy va usato come baseline funzionale, semantica e dati della transizione.
- Il legacy non va copiato ciecamente come struttura architetturale, come naming di dominio o come boundary target.

## Regola permanente di esplorazione del legacy
- Quando un bounded context dipende dal legacy, non fermarsi alla sola lettura delle tabelle.
- L'esplorazione deve includere, quando pertinente:
  - struttura DB legacy;
  - controller, model, service, library e helper rilevanti;
  - flow auth, sessione, ACL e runtime;
  - query e dipendenze operative;
  - view finali, partial, template e javascript collegato;
  - ordine operativo reale dei passaggi nelle maschere finali;
  - configurazioni e regole implicite che emergono dal flusso.
- Principio permanente:
  - il DB dice cosa esiste;
  - il codice dice come viene trattato;
  - le view e il flusso finale dicono come viene davvero usato.
- Nei bounded context a forte componente operativa, il flusso finale legacy e' sorgente semantica primaria insieme a DB e codice.

## Struttura a step
Ogni avanzamento avviene tramite step piccoli, verificabili e con stato esplicito, ma la granularita' deve restare umana e governabile.

Convenzione Step ID:
`MSN-<AREA>-<NUMERO>`

Esempi:
- `MSN-GOV-001`
- `MSN-AUTH-001`
- `MSN-CAT-002`

## Aree abilitate
- `GOV`
- `AUD`
- `AUTH`
- `ORG`
- `CAT`
- `DEAL`
- `UI`
- `API`
- `DOC`
- `NOTIF`
- `FIN`
- `SIGN`
- `E2E`
- `DEP`
- `SHARED`

## Stati ammessi
- `backlog`
- `ready`
- `in_corso`
- `bloccato`
- `validato`
- `chiuso`
- `no_go`

## Tipi di lavoro ammessi
- `audit`
- `design`
- `implementazione`
- `e2e`
- `deploy`
- `explain-only`

## Format obbligatorio degli step
Ogni step deve usare il seguente template minimo:

```md
## Step
- Step ID: MSN-AREA-001
- Titolo breve: ...
- Tipo di lavoro: audit | design | implementazione | e2e | deploy | explain-only
- Obiettivo: ...
- Perimetro incluso: ...
- Perimetro escluso: ...
- Input disponibili: ...
- Output atteso: ...
- Stato iniziale: backlog
- Done condition: ...
- Rischi principali: ...
- Step successivo consigliato: ...
```

## Regola di granularita' degli step
- Aprire uno step nuovo solo quando cambia davvero almeno uno tra:
  - bounded context;
  - tipo di lavoro;
  - deliverable verificabile;
  - rischio o boundary architetturale.
- Se il lavoro e' una sotto-decisione interna allo stesso deliverable, va mantenuto nello step corrente e documentato nel report, non promosso a nuovo step.
- Il master progress deve favorire macro-filoni leggibili e verificabili, evitando proliferazione di micro-step concettuali che riducono chiarezza operativa.

## Distinzione obbligatoria per tipo di lavoro
- `audit`: osserva il legacy, i flussi esistenti, i dati e i rischi senza introdurre soluzione definitiva.
- `design`: definisce modello target, boundary, contratti, regole e transizione senza implementare business logic completa.
- `implementazione`: realizza codice dentro un perimetro gia' chiarito da audit/design.
- `e2e`: verifica comportamento end-to-end e compatibilita' dei flussi critici.
- `deploy`: copre prerequisiti tecnici, setup, branching, release e note operative.
- `explain-only`: chiarisce stato, scelte o funzionamento senza cambiare artefatti runtime.

## Regole di reportistica
- Ogni task che produce avanzamento documentabile deve generare almeno un report in `_codex_reports`.
- I report devono essere orientati all'operativita': contesto, obiettivo, file toccati, decisioni, limiti, next step.
- La profondita' dei report deve essere proporzionata al task; evitare teoria astratta.
- I report architetturali o condivisi vanno sotto `_codex_reports/shared/architecture`.
- I report di setup/deploy/workspace vanno sotto `_codex_reports/infra/deploy`.
- Mantenere al massimo due livelli sotto `_codex_reports`.
- Ogni step con impatto dati deve produrre almeno questi contenuti espliciti:
  - schema target;
  - mapping legacy -> target;
  - strategia di import;
  - gap, collisioni e ambiguita' residue.
- La reportistica deve aiutare a chiudere macro-step leggibili, non a frammentare artificialmente il lavoro in una catena di micro-task.

## Governance legacy import
- Il legacy e' anche sorgente dati di riferimento durante la transizione, non solo baseline funzionale da osservare.
- Il nuovo schema Neo non deve nascere scollegato dal problema reale di caricamento dati dal DB legacy.
- Ogni bounded context nuovo deve distinguere sempre:
  - schema target corretto;
  - mapping legacy -> target;
  - strategia di importazione o migrazione;
  - punti non riconciliati automaticamente.
- I report di mapping e strategia import restano documenti di architettura e vanno in `_codex_reports/shared/architecture`.
- Le note operative di bootstrap o di esecuzione import restano documenti deploy/operativi e vanno in `_codex_reports/infra/deploy`.
- Il supporto tecnico condiviso per import legacy vive in `app/Application/LegacyImport`.
- Il supporto specifico di bounded context vive preferibilmente in `app/Application/<Context>/LegacyImport`.
- DTO, mapper e transformer di bounded context devono restare lato backend, non nel frontend.
- Eventuali comandi Artisan di import devono vivere in `app/Console/Commands/LegacyImport` e usare naming `legacy:import:<context>` o `legacy:dry-run:<context>`.
- Dry-run, commit gate, create-only pass, reconciliation residui e mapping tracing vanno letti come preparazione della migrazione finale big bang, non come lavoro isolato.

## Convenzioni mapping/import
- Naming file mapper:
  - `Legacy<Context><Entity>Mapper.php` quando la responsabilita' principale e' tradurre record legacy in shape target.
  - `Import<Context><Entity>FromLegacy.php` quando la responsabilita' principale e' orchestrare import idempotente.
- Naming classi di supporto:
  - `LegacyRecordReference`, `LegacySourcePointer`, `LegacyImportResult` per concetti tecnici condivisi.
- Naming task:
  - audit mapping: usare l'area del bounded context o `AUD` se trasversale;
  - design import: usare l'area del bounded context o `SHARED` se trasversale;
  - implementazione import: usare l'area del bounded context impattato.
- Naming report:
  - mapping/design: `YYYYMMDD_HHMMSS_macsolution-neo_architecture_<topic>.md`
  - operativo/deploy/import: `YYYYMMDD_HHMMSS_macsolution-neo_deploy_<topic>.md`

## Regola permanente di granularita'
- I task devono essere piccoli, verificabili e con done condition chiara, ma non eccessivamente granulari.
- Quando una decisione tecnica e' interna a un macro-step e non produce un risultato autonomo rilevante, va assorbita nello step padre.
- Aprire un nuovo filone solo se cambia davvero almeno uno tra:
  - bounded context;
  - tipo di lavoro;
  - deliverable verificabile;
  - rischio o boundary architetturale.

## Standard pratico per tracciare riferimenti legacy
- Usare un campo `legacy_id` solo quando esiste un riferimento semplice, stabile e uno-a-uno verso un record legacy rilevante per l'aggregate target.
- Aggiungere `legacy_source` quando esistono piu' sorgenti legacy o la provenienza non e' implicita.
- Aggiungere `legacy_table` o `legacy_key` solo se servono davvero a ricostruire provenienza o chiave composta senza ambiguita'.
- Usare una tabella ponte di mapping quando:
  - il mapping non e' uno-a-uno;
  - la chiave legacy e' composta;
  - serve supportare reimport idempotente o riconciliazione;
  - piu' sorgenti legacy confluiscono nello stesso aggregate Neo.
- Usare tracking tecnico esterno al dominio quando il riferimento legacy serve soprattutto a import, audit o riconciliazione tecnica e non e' parte del linguaggio di dominio.

## Tipi di caricamento dati
- `seed statici`: dati tecnici o stabili del progetto, non dipendenti dal legacy runtime.
- `bootstrap dati minimi`: dati minimi necessari per avvio locale o foundation iniziale.
- `import una tantum dal legacy`: caricamento controllato per popolare il nuovo schema.
- `sync transitorio`: allineamento temporaneo tra legacy e Neo durante convivenza.
- `reimport idempotente`: import rieseguibile che non duplica o corrompe i target gia' consolidati.

## Regole per perimetro incluso/escluso
- Il perimetro incluso deve dire chiaramente cosa viene coperto nel task.
- Il perimetro escluso deve dire chiaramente cosa non viene affrontato ora.
- Ogni esclusione serve a evitare widening, implementazioni premature e regressioni metodologiche.

## Done condition
Un task e' considerato done solo se:
- gli artefatti previsti sono stati creati o aggiornati;
- i path dichiarati sono corretti;
- lo stato e la done condition dello step sono verificabili;
- i report richiesti esistono nei path previsti;
- non sono stati introdotti widening o assunzioni non dichiarate.

## Regole di prudenza su legacy e transizione
- Il legacy va letto per capire comportamento, dati, eccezioni operative e vincoli reali.
- Le scorciatoie o incoerenze del legacy non vanno replicate in automatico.
- La transizione deve essere graduale, compatibile e osservabile.
- Dove il legacy diverge dal dominio corretto, il documento di lavoro deve renderlo esplicito.
- La compatibilita' dati non implica replica cieca di tabelle, naming o coupling storico.
- Non usare scorciatoie tecniche per far quadrare l'import tradendo il modello target.
- Distinguere sempre con chiarezza, quando emergono dal legacy:
  - internal platform users;
  - organizzazioni dealer;
  - venditori dealer-scoped;
  - commerciale;
  - account;
  - eventuali altri attori reali.

## Regola backend-first / API-first / mobile-ready
- Ogni modulo nuovo deve poter evolvere da backend/API chiari verso piu' client.
- Le capability di frontend non devono anticipare contratti backend non stabilizzati.
- Navigazione, permessi e composizione UI devono poter derivare da capability e stato, non da hardcode locale fragile.
- La shell UI puo' riusare pattern Velzon, ma la fonte di verita' del dominio resta il backend.
- La business logic di dominio resta nel backend; Vue e' un client del portale, non la fonte di verita' del modello.

## Regola flow-first semantics
- Nei bounded context dove la logica operativa e' forte, Codex deve considerare anche il flusso finale legacy come sorgente semantica.
- Questo vale in particolare per:
  - auth/org;
  - catalog/configurazioni;
  - dealer/configurazioni dealer;
  - preventivi;
  - pratiche;
  - documenti;
  - approvazione/backoffice.
- Se DB, codice e view raccontano storie diverse, la lettura corretta va ricostruita dal comportamento runtime finale e poi tradotta nel target Neo.

## Regola sul legacy
- Il legacy e' baseline funzionale, non baseline architetturale.
- In caso di conflitto tra struttura storica e modello target corretto, si documenta la differenza e si sceglie una strategia di transizione prudente.

## Regola contro widening non richiesti
- Non si aprono sotto-progetti, refactor o fondazioni extra se non direttamente richiesti o logicamente indispensabili al task corrente.
- Se emerge un tema importante ma fuori perimetro, si registra come step successivo consigliato o rischio.
- L'adozione di asset o pattern Velzon deve limitarsi a cio' che serve davvero al task corrente.

## Regola di verifica finale
- Prima di dichiarare concluso un task, verificare realmente i file creati o modificati.
- Nessun file deve essere riportato come esistente senza controllo effettivo del path nel workspace.
