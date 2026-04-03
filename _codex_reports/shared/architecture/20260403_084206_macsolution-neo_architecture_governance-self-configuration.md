# Contesto
Il workspace Mac Solution Neo ha gia' consolidato bootstrap Laravel + Vue, governance base, filone legacy import e diversi slice `AUTH/ORG`.

Con l'avanzare del progetto, alcune regole sono ormai stabili e non devono piu' essere ribadite in ogni prompt.

# Obiettivo del task
Rendere permanenti in `AGENTS.md` e `AI_CONTEXT.md` le regole di lavoro che devono guidare i prossimi macro-step del progetto.

# Regole permanenti consolidate
- Distinzione obbligatoria tra:
  - comportamento legacy reale;
  - modello target corretto;
  - strategia di transizione compatibile.
- Legacy come baseline funzionale, semantica e sorgente dati della transizione, ma non come baseline architetturale da copiare.
- Approccio permanente:
  - Laravel + Vue;
  - backend-first / API-first;
  - mobile-ready.
- Ogni bounded context con impatto dati deve lasciare sempre traccia di:
  - schema target;
  - mapping legacy -> target;
  - strategia di import o migrazione;
  - gap, collisioni, ambiguita', record non mappabili automaticamente;
  - chiavi di riconciliazione o legacy references.
- I task devono essere piccoli e verificabili, ma non microscopici:
  - le micro-decisioni tecniche interne a uno step padre vanno assorbite nello step;
  - non devono generare automaticamente nuovi filoni autonomi.
- Tutta la reportistica deve vivere solo in `_codex_reports`, con timestamp completo iniziale.

# Regola permanente di esplorazione del legacy
E' stata resa esplicita una regola piu' forte di lettura del legacy:
- non basta leggere le tabelle;
- quando il bounded context dipende dal legacy bisogna esplorare anche:
  - controller, model, library, helper;
  - auth, sessione, ACL, query runtime;
  - view finali, partial, template, javascript;
  - ordine operativo reale dei passaggi;
  - configurazioni e regole implicite emergenti dal flusso finale.

Principio operativo fissato:
- il DB dice cosa esiste;
- il codice dice come viene trattato;
- le view e il flusso finale dicono come viene davvero usato.

# Regola permanente su import legacy -> Neo
La governance ribadisce ora in modo stabile che import, reconciliation e mapping non sono un filone opzionale:
- sono parte strutturale del progetto;
- valgono come default per tutti i bounded context data-impact;
- non richiedono piu' istruzioni ripetitive nei prompt futuri.

# Big bang preparation
E' stato chiarito in modo permanente che:
- dry-run;
- commit gate;
- create-only pass;
- reconciliation residui;
- tracing dei mapping;
sono strumenti preparatori della migrazione finale big bang, non esperimenti isolati.

# Aggiornamenti fatti
- `AGENTS.md`
  - rafforzata la governance permanente su legacy vs target;
  - introdotta la regola di esplorazione flow-first del legacy;
  - chiarita la preparazione al big bang;
  - chiarita la granularita' corretta dei task;
  - rafforzate le regole permanenti su import, reportistica e prudenza semantica.
- `AI_CONTEXT.md`
  - chiarito il ruolo del legacy come sorgente funzionale, semantica e dati;
  - introdotta una sezione permanente su esplorazione legacy;
  - introdotta una sezione permanente su preparazione al big bang;
  - rafforzata la regola flow-first semantics;
  - ribadita la regola che i micro-passaggi tecnici restano nel macro-step padre.

# Come cambiano i prossimi task
Da ora i prossimi task dovrebbero nascere gia' orientati cosi':
- macro-step leggibili per bounded context;
- audit del legacy condotto su DB + codice + view/flow finale;
- schema target e import strategy trattati insieme, non separati artificialmente;
- reportistica in `_codex_reports` come parte obbligatoria del deliverable;
- nessuna proliferazione di micro-task se non cambia davvero il boundary.

# Limiti o chiarimenti futuri
- Questa auto-configurazione non sostituisce gli audit specifici dei singoli bounded context.
- Non definisce da sola il target di dominio dei prossimi moduli.
- Serve come cornice permanente, non come sostituto del lavoro architetturale contestuale.
