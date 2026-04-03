# AI_CONTEXT.md

## Visione del progetto
Mac Solution Neo e' la nuova piattaforma destinata a sostituire progressivamente il sistema legacy Mac Solution basato su CodeIgniter 3, con una base architetturale piu' chiara, modulare e sostenibile nel tempo.

## Obiettivo strategico
Costruire una piattaforma Laravel + Vue orientata a bounded context chiari, servizi backend affidabili e contratti applicativi stabili, pronta a sostenere sia il frontend web sia una futura app mobile senza riscritture strutturali.

## Regole permanenti consolidate
- Laravel + Vue restano stack target permanente del workspace Neo.
- L'approccio corretto resta backend-first / API-first / mobile-ready.
- Il legacy CI3 resta baseline funzionale e operativa da esplorare, non baseline architetturale da copiare.
- Ogni bounded context va letto e costruito distinguendo sempre tra:
  - comportamento legacy osservato;
  - modello target corretto;
  - strategia di transizione compatibile.
- I task devono essere piccoli e verificabili, ma non microscopici:
  - le micro-decisioni interne a uno stesso deliverable non devono diventare automaticamente step autonomi;
  - la roadmap deve restare leggibile per macro-filoni e bounded context.
- Ogni bounded context con impatto dati deve lasciare memoria permanente di:
  - schema target;
  - mapping legacy -> target;
  - strategia di importazione o migrazione;
  - gap, ambiguita', collisioni e chiavi di riconciliazione.
- I task devono essere piccoli e verificabili ma non microscopici:
  - le micro-scelte tecniche interne a un macro-step vanno assorbite nello step padre;
  - la roadmap deve restare leggibile per macro-filoni.
- Tutta la reportistica di progetto deve vivere solo in `_codex_reports`.
- Dry-run, reconciliation, commit gate, create-only pass e mapping tracing fanno parte della preparazione alla migrazione finale big bang, non sono filoni isolati.

## Relazione con Mac Solution legacy
- Il legacy e' la baseline funzionale e operativa da esplorare.
- Il legacy e' anche sorgente semantica e sorgente dati della transizione.
- Il legacy non e' la baseline architetturale da replicare.
- Ogni analisi deve separare:
  - comportamento legacy osservato;
  - modello di dominio target corretto;
  - strategia di transizione graduale e compatibile.

## Regola permanente di esplorazione legacy
- Quando un bounded context dipende dal legacy, l'analisi non deve fermarsi al DB.
- L'esplorazione corretta combina:
  - struttura DB;
  - controller, model, library, helper e query runtime;
  - auth, sessione, ACL, configurazioni e dipendenze implicite;
  - view finali, partial, template, javascript e ordine operativo delle maschere.
- Principio da mantenere sempre:
  - il DB dice cosa esiste;
  - il codice dice come viene trattato;
  - le view e il flusso finale dicono come viene davvero usato.
- Nei bounded context con logica operativa forte, il flow legacy finale e' sorgente semantica primaria per mapping, redesign e import.

## Direzione Laravel + Vue
- Laravel come fondazione applicativa e dominio backend.
- Vue come client della nuova piattaforma, guidato da capability e contratti backend.
- Approccio backend-first / API-first per evitare accoppiamenti UI-premature.
- La shell UI attuale usa Velzon come base visuale di riferimento, riadattata dentro la struttura del progetto e non ereditata come architettura.

## Backend-first / API-first / mobile readiness
- Le responsabilita' di business devono essere chiarite lato backend prima della composizione UI.
- Le API vanno pensate come contratti riusabili anche da client mobile futuri.
- Le decisioni di modello, autorizzazione, assegnazione e navigazione devono restare compatibili con piu' canali.
- Il frontend web attuale e' una shell autenticata che consuma stato e capability condivise dal backend.

## Stato bootstrap reale del portale
- Il workspace contiene ora un bootstrap Laravel + Vue eseguibile in locale.
- L'autenticazione base e' attiva con login, logout, profilo utente, area riservata e foundation ruoli bootstrap non di dominio.
- E' presente una base API versionata su `/api/v1`.
- La shell UI del portale e' stata riallineata su pattern e asset utili di Velzon, mantenendo separazione pulita da business logic e dominio.
- Il database iniziale copre utenti, token personali e ruoli bootstrap; organization e ACL finale restano step successivi.
- L'ambiente locale di riferimento per il bootstrap e' Windows con Laragon, senza dipendenze da Docker/Sail/Valet.
- Il workspace e' stato consolidato dopo il bootstrap: gli artefatti temporanei non fanno piu' parte della base applicativa da evolvere.
- E' presente un primo slice shared auth/org sul contesto autenticato:
  - `/api/v1/me` espone anche un `context` bootstrap-safe;
  - Inertia condivide `auth.context`;
  - la navigation backend-driven usa metadata piu' stabili per evolvere verso scope futuri.
- E' ora presente anche una persistence minima foundation per organization membership:
  - tabella `organizations`;
  - tabella `organization_memberships`;
  - seed locale con workspace organization bootstrap;
  - resolver del contesto autenticato agganciato a membership attive.
- E' ora presente anche una persistence minima reale per AUTH/ORG foundation:
  - `organizations.parent_organization_id` come bridge organizzativo prudente;
  - tabella `dealer_operator_assignments` per assegnazioni minime dealer -> operatore;
  - `account_status` e `context.assignments` esposti nel contract shared;
  - `/api/v1/me` e Inertia leggono ora dati persistiti piu' vicini al foundation target.
- Il resolver del context segue ora una regola esplicita e prudente:
  - preferire la membership primaria attiva;
  - in assenza di primary, usare la prima membership attiva disponibile;
  - esporre nel contract anche metadata read-only di context switching.
- E' presente anche uno switching minimo del context attivo:
  - selezione di una membership attiva valida;
  - persistenza leggera in sessione autenticata;
  - fallback automatico a `primary membership first` se non esiste selezione valida.
- Capability e navigation sono ora sensibili anche al context attivo:
  - le capability shared possono derivare da ruolo tecnico, membership role e stato del context;
  - la navigation puo' esprimere scope `global` o `contextual`;
  - gli item context-aware dipendono da capability e organization attiva, non da branching locale del frontend.

## Principi di migrazione
- Minimum migration risk: transizione incrementale, osservabile e prudente.
- Maximum architectural clarity: evitare di portare nel nuovo workspace ambiguita' storiche non necessarie.
- Compatibilita' dati senza replica cieca: preservare continuita' operativa senza copiare automaticamente naming, struttura o coupling del legacy.
- Ogni bounded context nuovo deve lasciare traccia esplicita di:
  - schema target;
  - mapping legacy -> target;
  - strategia di importazione o migrazione dati;
  - gap, collisioni, ambiguita' e dati non mappabili automaticamente.

## Big bang preparation
- La migrazione finale attesa del workspace Neo e' di tipo big bang.
- I filoni di import e reconciliation vanno quindi costruiti come preparazione controllata a quel momento finale.
- Questo significa che:
  - dry-run;
  - commit gate;
  - create-only pass;
  - reconciliation dei residui;
  - tracing su mapping tecnici;
  - chiavi legacy di riconciliazione;
  sono da leggere come pezzi di preparazione della migrazione completa e non come iniziative isolate.

## Governance legacy import -> Neo
- Il problema import legacy -> Neo e' ora un filone permanente di governance tecnica e documentale del workspace.
- Il legacy e' sorgente dati di riferimento nella fase di transizione: il nuovo schema non deve essere progettato ignorando come i dati verranno caricati.
- La distinzione corretta da mantenere e':
  - schema target Neo;
  - mapping dal legacy;
  - strategia di caricamento;
  - riconciliazione dei casi non mappabili in automatico.
- I report di mapping e strategia import devono vivere in `_codex_reports/shared/architecture`.
- Le note operative di esecuzione o setup import devono vivere in `_codex_reports/infra/deploy`.
- Il supporto tecnico condiviso per import legacy vive in `app/Application/LegacyImport`.
- Il supporto specifico di bounded context vive preferibilmente in `app/Application/<Context>/LegacyImport`.
- Questa regola e' default progettuale del workspace:
  - non va piu' ripetuta nei prompt futuri per ogni bounded context data-impact.

## Standard pratico di tracking legacy references
- `legacy_id` va usato solo quando il riferimento legacy e' semplice, stabile e uno-a-uno.
- `legacy_source` va usato quando esistono piu' sorgenti o quando la provenienza non e' ovvia.
- `legacy_table` e `legacy_key` vanno usati solo quando servono davvero a riconciliare chiavi composte o origini non banali.
- Una mapping table dedicata e' preferibile quando:
  - il mapping non e' uno-a-uno;
  - la chiave legacy e' composta;
  - piu' sorgenti confluiscono nello stesso aggregate;
  - serve reimport idempotente o audit tecnico dei match.
- Il tracking tecnico esterno al dominio e' preferibile quando il riferimento legacy non appartiene al linguaggio di dominio, ma serve a import e riconciliazione.

## Tipi di caricamento dati da distinguere
- Seed statici
- Bootstrap dati minimi
- Import una tantum dal legacy
- Sync transitorio durante convivenza
- Reimport idempotente e verificabile

## Struttura minima introdotta per il filone import
- `app/Application/LegacyImport/Contracts/LegacyToTargetMapper.php`
- `app/Application/LegacyImport/Support/LegacyRecordReference.php`
- Questa base non introduce ancora ETL framework o runtime import, ma fissa namespace e concetti condivisi per i prossimi bounded context.

## Bounded context principali
- Identity e authentication
- Organization e struttura operativa
- Catalogo
- Dealer, commerciali e account
- Preventivi
- Pratiche
- Documenti
- Notifiche
- Finanziamenti
- Firma
- Componenti shared e capability trasversali

## Regola di avanzamento per bounded context
- I bounded context vanno aperti e consolidati per macro-step comprensibili e verificabili.
- Non serve trasformare ogni raffinamento interno di uno stesso filone in un nuovo sotto-progetto concettuale.
- Quando il boundary non cambia, e il deliverable resta lo stesso, e' preferibile assorbire la decisione nel macro-step corrente e documentarla bene nei report.
- Questo vale in particolare per:
  - identity + organization foundation;
  - catalog foundation;
  - enablement e pricing profile;
  - filoni legacy import read-only prima del commit mode.

## Flow-first semantics
- Nei bounded context dove il comportamento operativo reale pesa piu' della sola struttura dati, il flow finale legacy va trattato come sorgente semantica.
- Questo vale soprattutto per:
  - auth/org;
  - catalog/configurazioni;
  - dealer/configurazioni dealer;
  - preventivi;
  - pratiche;
  - documenti;
  - approvazione/backoffice.
- Se il DB, il codice e la UI finale divergono, la traduzione verso Neo va guidata dal comportamento effettivo osservato nel flusso completo.

## Ordine di lavoro raccomandato ad oggi
Dopo il primo audit consolidato su identity + organization, l'ordine raccomandato e':
1. governance e documentazione persistente
2. audit/design di identity + organization foundation
3. definizione capability shared di API, permessi, assegnazioni e sidebar
4. audit/design di catalog foundation
5. governance permanente del filone legacy import -> Neo
6. apertura progressiva dei moduli business veri e propri con schema + mapping + import strategy espliciti

## Errori legacy noti da non trascinare
Dall'audit legacy iniziale su identity + organization risultano gia' da non trascinare:
- coupling improprio tra UI, permessi e logica applicativa;
- identita' diverse a seconda del canale (`admin`, `dealer`, API JWT dealer) invece di un account model unificato;
- ruoli semantici e runtime letti direttamente da sessione o helper applicativi;
- appartenenza organizzativa implicita sul dealer corrente invece che su membership esplicite;
- assignment operativi distribuiti tra record dealer, tabelle dedicate e stato di sessione;
- ACL introdotta nel legacy come layer utile ma ancora ibrido rispetto ai modelli storici.

## Compatibilita' dati senza replica cieca
- La compatibilita' operativa e di migrazione non richiede replica uno-a-uno del disegno legacy.
- Mapping, adapter e strategie di transizione sono preferibili alla copia cieca di modelli errati o opachi.

## Note su identity + organization foundation
- Identity e organization sono foundation prioritaria perche' influenzano autenticazione, ruoli, permessi, assegnazioni, ownership e visibilita'.
- Prima di implementare moduli business serve chiarire almeno: utente, ruolo, permesso, organizzazione, unita' operativa, assegnazione e capability.
- La convivenza tra legacy e nuova piattaforma richiedera' probabilmente mapping chiaro tra identita' storiche e modello target.
- La base attuale copre solo utenti e ruoli bootstrap tecnici; non va confusa con il modello finale di identity + organization.
- La persistence minima introdotta non rappresenta ancora il modello organization finale: serve solo a dare un primo supporto reale e verificabile a membership e contesto attivo.
- La persistence minima auth/org ora copre anche un bridge organizzativo prudente:
  - una organization puo' essere figlia di un'altra organization tramite `parent_organization_id`;
  - il bridge serve a leggere in modo minimo relazioni tipo `workspace -> dealer` senza congelare l'intero organization domain.
- Il target model raccomandato distingue almeno tra:
  - identity account: soggetto autenticabile della piattaforma;
  - organization membership: appartenenza di un account a una o piu' organizzazioni;
  - assignment: legame operativo tra persona, contesto organizzativo e responsabilita';
  - capability: autorizzazione effettiva, preferibile a hardcode UI o sola semantica di ruolo.
- I ruoli bootstrap tecnici attuali sono utili solo come base iniziale e andranno evoluti verso ruoli scoped e membership-aware.
- Organization non dovrebbe essere un semplice attributo del record `users`, ma un boundary dedicato con relazioni esplicite.
- Il target model organization raccomandato introduce almeno:
  - organization aggregate come contenitore istituzionale o commerciale rilevante per il dominio;
  - membership come appartenenza di un account a una organization;
  - organization unit o scope operativo come livello di segmentazione interna;
  - assignment come responsabilita' contestuale su clienti, pratiche, dealer o altri oggetti futuri.
- Membership e assignment non vanno confusi:
  - membership abilita appartenenza e perimetro di visibilita';
  - assignment abilita responsabilita' operative specifiche.
- Nel workspace esiste ora una prima persistence minima di assignment:
  - `dealer_operator_assignments`
  - relazione tra dealer organization e membership operativa
  - `assignment_role_code` iniziale per figure tipo `dealer_account_manager` o `dealer_commercial`
  - uso volutamente limitato a foundation shared e futura riconciliazione, non a modulo business completo
- Le capability effettive dovrebbero dipendere da ruolo scoped, membership, assignment e policy backend.
- L'audit legacy ha confermato alcuni fatti operativi utili:
  - il backoffice `admin` autentica oggi su record `dealer` con `tipo = admin`;
  - l'area `dealer` usa una sessione distinta con `dealer` e `user_logged`;
  - l'API legacy usa JWT separati e tratta di fatto il dealer come principal applicativo;
  - commerciali e account esistono come entita' dedicate e come assignment verso dealer, non come semplice ruolo globale.
- Questo conferma che Neo non dovrebbe modellare identity e organization come un solo record utente con pochi flag.
- Correzione foundation ormai esplicita:
  - nel legacy i venditori del dealer emergono in `dealer_collaboratore`;
  - non vanno trattati come dettaglio del dealer-organization;
  - in Neo vanno trattati come `users` autenticabili con membership sul dealer.
- Evidenze legacy mirate sui venditori:
  - `dealer_collaboratore` contiene `username`, `password`, `password_value`, `email`, `ruolo`, `is_enable` e `id_dealer`;
  - nel dataset reale locale risultano 668 record, tutti con `username` e `password`;
  - i ruoli legacy osservati sono soprattutto `collaboratore` e `amministratore`;
  - questo rende i dealer seller/dealer admin attori identitari reali del foundation AUTH/ORG, non semplici note organizzative.

## Note su identity + organization import governance
- Primo schema target minimo da presidiare:
  - `users` come account autenticabile Neo;
  - `organizations` come aggregate organizzativo esplicito;
  - `organization_memberships` come legame account -> organization;
  - ruoli scoped e assignment come layer successivi, non da collassare nel primo import foundation.
- Primo mapping legacy -> target raccomandato:
  - record `dealer` usati come `admin` nel backoffice non vanno copiati come "organization", ma riletti come identita' legacy da ricondurre a `account` Neo con contesto organizzativo da determinare;
  - record `dealer` usati nell'area dealer vanno distinti tra soggetto organizzativo e utenza effettiva, evitando di trattare il dealer stesso come unico principal definitivo;
  - figure operative `commerciale` e `account` vanno considerate indizi per futuri assignment, non campi da schiacciare subito nel primo import account/membership;
  - ACL legacy recente va trattata come sorgente di mapping permissionale e di audit, non come schema finale da importare uno-a-uno.
- Prima strategia di import raccomandata:
  - partire da dry-run read-only con candidate resolution, matching e metriche verificabili;
  - distinguere sempre tra candidate risolti, target Neo gia' esistenti e match solo ipotetici;
  - introdurre commit mode solo dopo aver chiarito confidenza, collisioni e riconciliazione.
- Primo livello di confidence scoring auth/org da presidiare nei prossimi slice:
  - `high` per match con segnali forti e coerenti oppure mapping tecnico gia' verificato;
  - `medium` per match con un segnale robusto ma senza conferma ridondante;
  - `low` per match guidati da segnali deboli o parziali da sottoporre a prudenza;
  - `none` quando non esiste base sufficiente per dichiarare un match affidabile.
- Regola prudenziale permanente:
  - un `ambiguous_match` non deve mai essere promosso a confidence `high`;
  - edge case, collisioni e segnali deboli devono abbassare la confidence o fermare il match;
  - la confidence non sostituisce `match_status`, ma lo qualifica per il dry-run e per la futura riconciliazione.
- Slice runtime ora raccomandato:
  - esporre `match_confidence` nel dry-run auth/org;
  - aggregare metriche `high`, `medium`, `low`, `none`;
  - mantenere la confidence come layer tecnico di lettura, non come decisione automatica di commit.
  - fase 1: import tecnico delle identita' legacy candidate verso `users`;
  - fase 2: import o riconciliazione delle organization minime;
  - fase 3: costruzione delle membership account -> organization;
  - fase 4: solo dopo, riallineamento di ruoli scoped, capability e assignment.
- Tracking legacy references raccomandato per questo bounded context:
  - evitare di sporcare subito `users` con troppi campi `legacy_*`;
  - preferire mapping table dedicate quando la provenienza legacy e' ambigua o multi-sorgente;
  - usare `legacy_id` diretto solo per riferimenti semplici e stabili.
- Primo mapping legacy -> target aggiornato per la persistence minima auth/org:
  - `dealer` legacy puo' mappare a `organizations` di tipo `dealer` quando rappresenta davvero il soggetto organizzativo/commerciale;
  - `dealer_collaboratore` legacy va trattato come sorgente primaria di account autenticabili dealer-scoped:
    - `collaboratore` -> `users` + `organization_memberships.role_code = dealer_seller`;
    - `amministratore` -> `users` + `organization_memberships.role_code = dealer_admin`;
  - operatori storici e figure tipo `commerciale/account` non vanno collassati nel dealer, ma ricondotti a `users` + `organization_memberships`;
  - assegnazioni legacy dealer -> operatore vanno lette come candidate per `dealer_operator_assignments`, non come semplice ruolo globale o stato di sessione.
- Gap gia' noti da trattare con prudenza:
  - principal legacy non unificato tra admin, dealer e API JWT;
  - dealer usato talvolta come account, talvolta come organization;
  - venditori dealer con email mancanti o placeholder, ma con credenziali legacy reali;
  - assignment commerciali/account distribuiti tra tabelle e sessione;
  - semantica dei ruoli storici non abbastanza pulita da essere migrata direttamente.

## Note su auth/org foundation persistence minimum
- Questo macro-step ha consolidato una persistence minima reale e riusabile per:
  - account autenticabile;
  - membership minima;
  - bridge organizzativo leggero;
  - assegnazione dealer -> operatore.
- Schema target minimo attuale:
  - `users`
  - `organizations`
  - `organization_memberships`
  - `dealer_operator_assignments`
- Motivazione:
  - distinguere identita', appartenenza e responsabilita' operative senza aprire ancora il domain completo di organization.
- Correzione ora consolidata:
  - il dealer resta una `organization`;
  - i venditori del dealer sono account autenticabili di primo livello;
  - il loro legame col dealer passa per `organization_memberships`, non per campi annidati nel dealer;
  - questo foundation lascia distinto anche il layer `dealer_operator_assignments`, che resta per responsabilita' operative e non per modellare l'identita' del venditore.
- Shared contract allineato:
  - `context.active_organization` espone anche il `parent_organization` quando presente;
  - `context.assignments` espone assegnazioni persistite del membership attivo;
  - capability shared possono derivare anche da assignment minimi persistiti.
- Il contract shared attuale rappresenta correttamente anche il caso venditore foundation:
  - membership dealer attiva;
  - `role_code` dealer-scoped (`dealer_seller` o `dealer_admin`);
  - organization dealer come `active_organization`;
  - nessuna dipendenza da ruoli globali o dashboard business premature.

## Note su identity + organization legacy reference persistence
- Per `users` e' prudente evitare subito campi `legacy_*` multipli nel dominio principale, perche' la provenienza puo' essere:
  - admin legacy su `dealer`;
  - user area dealer;
  - principal API JWT;
  - eventuali figure operative successive.
- Per `users` la scelta raccomandata e' una mapping table tecnica dedicata, utile a:
  - conservare multi-sorgente;
  - supportare riconciliazione manuale;
  - rendere idempotente il reimport.
- Per `organizations` e' accettabile un `legacy_id` semplice solo quando l'equivalenza con il dealer legacy e' stabile, uno-a-uno e non ambigua.
- Se la stessa organization Neo puo' derivare da piu' sorgenti legacy o da merge successivi, anche `organizations` dovrebbero usare mapping table tecnica dedicata.
- Per `organization_memberships` la scelta prudente e' quasi sempre una mapping table tecnica:
  - la membership spesso nasce da join o regole di riconciliazione;
  - non e' detto che esista un record legacy uno-a-uno semanticamente equivalente.
- Tracking tecnico minimo raccomandato per auth/org:
  - `source_system`
  - `legacy_table`
  - `legacy_key` o chiave composta serializzata
  - `target_type`
  - `target_id`
  - `mapping_status`
  - `last_imported_at`
  - eventuale `checksum` o fingerprint tecnico se serve reimport evolutivo
- Regola pratica:
  - `legacy_id` diretto nel dominio solo per equivalenze semplici e stabili;
  - mapping table tecnica per multi-sorgente, join complesse, riconciliazione e idempotenza;
  - tracking esterno al dominio quando il riferimento legacy serve all'import e non al linguaggio di business.
- Nel workspace esiste ora anche una persistence tecnica minima dedicata:
  - tabella `legacy_entity_mappings`
  - model `LegacyEntityMapping`
  - relazioni tecniche polimorfiche verso `users`, `organizations`, `organization_memberships`
- Questa persistence non rappresenta dominio business:
  - serve a tracing tecnico;
  - supporta idempotenza e riconciliazione;
  - evita di inquinare subito gli aggregate principali con campi `legacy_*` multipli.

## Note su identity + organization import command contract
- Il primo comando auth/org non deve essere ancora un import completo, ma un contratto operativo minimo e verificabile.
- Naming raccomandato:
  - `legacy:import:auth-org`
  - eventuale dry-run tramite opzione `--dry-run`
- Input minimi attesi del comando:
  - `source-system`
  - `legacy-table` o dataset dichiarato
  - limite opzionale di batch
  - modalita' `dry-run` oppure `commit`
- Output tecnico minimo atteso:
  - record letti
  - record candidati a `users`
  - record candidati a `organizations`
  - membership candidate
  - mapping creati
  - mapping aggiornati
  - collisioni o record non riconciliati
  - esito finale per batch
- Regole minime di idempotenza:
  - nessuna duplicazione se `legacy_key_hash` e target coincidono;
  - aggiornamento controllato del mapping se il target resta coerente;
  - segnalazione esplicita se la stessa legacy key punta a un target incompatibile.
- Regole minime di dry-run:
  - nessuna scrittura su aggregate target;
  - nessuna scrittura su `legacy_entity_mappings`;
  - produzione di riepilogo tecnico sufficiente per audit e riconciliazione.
- Regole di perimetro:
  - il primo comando non decide ancora ruoli scoped, capability finali o assignment avanzati;
  - il primo comando non apre sincronizzazione bidirezionale o import modulo completo.
- Nel workspace esiste ora anche un primo comando tecnico reale:
  - `legacy:import:auth-org --dry-run`
  - dataset controllato iniziale `bootstrap-auth-org`
  - nessuna scrittura su aggregate target o mapping in questa fase
- Il comando attuale serve a verificare:
  - forma dell'input
  - shape dell'output tecnico
  - presenza di collisioni e casi unresolved
  - comportamento esplicitamente read-only
  - separazione esplicita tra summary di matching e snapshot di reconciliation

## Note su identity + organization dataset adapter design
- Il prossimo passo corretto non e' sostituire subito il comando, ma introdurre un adapter legacy reale che rispetti lo stesso contratto del dry-run attuale.
- Regola di progetto:
  - il comando `legacy:import:auth-org` resta il punto di ingresso stabile;
  - il dataset controllato resta fallback di sicurezza;
  - l'adapter legacy reale deve cambiare solo la sorgente dati, non la shape dell'output tecnico.
- Contratto minimo raccomandato per l'adapter:
  - `supports(dataset)` per distinguere dataset controllato e adapter reale;
  - `fetch(sourceSystem, legacyTable, batch)` per restituire righe gia' normalizzate nel formato atteso dal dry-run.
- Primo target prudente dell'adapter reale:
  - lettura read-only di candidati auth/org dal legacy;
  - nessuna scrittura;
  - nessuna trasformazione completa di dominio;
  - nessun accoppiamento diretto tra command e query CI3 sparse.
- Vincolo importante:
  - anche con adapter reale, il comportamento iniziale deve restare `dry-run only`.
- Nel workspace esiste ora un primo adapter legacy reale read-only:
  - `MySqlAuthOrgLegacyDatasetAdapter`
  - dataset supportato: `legacy-ci3-auth-org`
  - configurazione dedicata in `config/legacy_import.php`
  - connessione Laragon legacy configurabile via env `LEGACY_IMPORT_*`
- Il comando mantiene due percorsi sicuri:
  - dataset controllato `bootstrap-auth-org`
  - adapter reale read-only `legacy-ci3-auth-org`
- Il contratto del comando non cambia:
  - stesso input
  - stesso output tecnico
  - nessuna scrittura in questa fase

## Note su identity + organization candidate resolution
- Il prossimo livello sopra l'adapter non deve ancora creare target Neo, ma deve migliorare la lettura dei candidati legacy.
- La candidate resolution read-only deve distinguere almeno:
  - `candidate_type`
  - `resolution_status`
  - `resolution_reason`
- Esempi di `candidate_type` iniziali:
  - `user`
  - `organization`
  - `membership`
  - `unknown`
- Esempi di `resolution_status` iniziali:
  - `mapped_candidate`
  - `needs_review`
  - `collision`
  - `unresolved`
- Obiettivo:
  - togliere logica grezza dal comando;
  - rendere piu' leggibile il dry-run;
  - preparare i futuri match verso `users`, `organizations`, `organization_memberships` senza introdurre ancora commit mode.
- Nel workspace esiste ora un contratto tecnico minimo per questo layer:
  - `App\Application\Auth\LegacyImport\Contracts\AuthOrgLegacyCandidateResolver`
- E' ora presente anche una prima implementazione read-only:
  - `DefaultAuthOrgLegacyCandidateResolver`
  - agganciata al `dry-run` auth/org
  - capace di produrre `candidate_type`, `resolution_status` e `resolution_reason`
- Il dry-run espone ora anche metriche piu' leggibili:
  - `needs_review`
  - `resolved_candidates`

## Note su identity + organization candidate matching
- Il matching read-only e' il livello successivo alla candidate resolution.
- Non deve ancora creare o aggiornare target Neo.
- Deve chiarire almeno:
  - `match_status`
  - `match_reason`
- Esempi iniziali di `match_status`:
  - `matched_existing_target`
  - `no_existing_match`
  - `ambiguous_match`
  - `not_applicable`
- Obiettivo:
  - capire se un candidato legacy risolto ha gia' un equivalente Neo plausibile;
  - preparare il futuro merge/import senza introdurre ancora scritture;
  - evitare che il comando salti direttamente da candidate resolution a import.
- Il matching dovra' essere prudente e target-aware:
  - `user` confrontato con `users`
  - `organization` confrontata con `organizations`
  - `membership` confrontata con `organization_memberships`
- Nel workspace esiste ora il contratto tecnico minimo per questo layer:
  - `App\Application\Auth\LegacyImport\Contracts\AuthOrgLegacyCandidateMatcher`
- E' ora presente anche una prima implementazione read-only:
  - `DefaultAuthOrgLegacyCandidateMatcher`
  - agganciata al dry-run auth/org
  - capace di produrre `match_status` e `match_reason`
- Il dry-run espone ora anche metriche di matching:
  - `matched_existing_targets`
  - `unmatched_candidates`
  - `ambiguous_matches`

## Note su auth/org import reconciliation foundation
- Il workspace ha ora una foundation tecnica reale di reconciliation auth/org sopra il dry-run read-only.
- Base tecnica condivisa gia' presente e ora effettivamente usata:
  - `legacy_entity_mappings`
  - `legacy_key_hash`
  - candidate resolution
  - matching
  - reconciliation snapshot separato dal summary
- Distinzione operativa consolidata:
  - `matching`: capire se il candidato legacy ha un target Neo plausibile;
  - `mapping`: tracing tecnico idempotente su `legacy_entity_mappings`;
  - `dry-run`: lettura, normalizzazione, matching e reporting senza scritture distruttive;
  - `commit mode`: volutamente non ancora disponibile.
- Regole di matching iniziali presidiate:
  - `user` legacy -> `users` via email normalizzata o mapping tecnico;
  - `dealer` legacy -> `organizations` via code normalizzato o mapping tecnico;
  - appartenenze legacy -> `organization_memberships` via coppia `user + organization`;
  - assegnazioni dealer -> operatore -> `dealer_operator_assignments` via tripletta `user + dealer + assignment_role_code`.
- Output tecnico di reconciliation ora leggibile:
  - sorgenti legacy usate;
  - chiavi di matching adottate;
  - candidati auto-matchati;
  - candidati importabili ad alta confidenza;
  - candidati che richiedono review manuale;
  - commit mode esplicitamente disabilitato.
- Ambiguita' ancora aperte:
  - dealer legacy talvolta principal, talvolta organization;
  - assignment legacy spesso poco espliciti o incompleti;
  - figure `commerciale/account` non ancora pienamente normalizzate nel legacy.

## Note su auth/org import dry-run real dataset
- Il dry-run reale e' stato eseguito sul dataset legacy locale `sql1483615_1`.
- Sorgenti realmente lette nel dry-run:
  - `dealer`
  - `dealer_collaboratore`
  - `commerciali`
  - `dealer_operatore_figura`
- Micro-fix applicati durante la validazione reale:
  - normalizzazione prudente delle email legacy sporche o placeholder;
  - fallback CLI del dataset adapter per ambiente Laragon quando la connessione PDO legacy non e' configurata con credenziali utilizzabili.
- Esito complessivo del dry-run reale:
  - `records_read = 1621`
  - `matched_existing_targets = 0`
  - `ready_create_candidates = 595`
  - `ambiguous_matches = 1026`
  - `batch_result = needs_reconciliation`
- Breakdown attuale:
  - `user`: 160 `ready_create`, 521 `needs_review`
  - `organization`: 274 `ready_create`
  - `membership`: 159 `ready_create`, 505 `needs_review`
  - `assignment`: 2 `ready_create`
- Evidenze critiche emerse dal dataset reale:
  - 16 dealer `admin` senza email valida => forte ambiguita' identitaria;
  - 302 `dealer_collaboratore` senza email valida;
  - 2 username dealer duplicate (`giuseppe`, `Direzionalemac`);
  - 10 email duplicate nei collaboratori, con il placeholder `-` ripetuto 204 volte.
- Valutazione corrente:
  - nessun commit mode;
  - commit reale AUTH/ORG ancora `NO-GO` finche' non si riducono le ambiguita' sui segnali identity/membership.
- Signal hardening ora consolidato sopra questa base:
  - placeholder identity come `-`, `null` o `n/a` vengono esclusi dai segnali affidabili;
  - `dealer_collaboratore` puo' restare trattabile come candidato `ready_create` anche senza email valida, ma solo quando esiste un segnale prudente `dealer_code + username`;
  - il `dealer admin` legacy senza segnale identitario forte resta caso speciale da review manuale;
  - il pairing membership distingue ora meglio tra coppia forte `email + organization_code`, pairing dealer-scoped prudente e pairing incompleto da review.
- Dopo il commit `create-only` e il dependency guard, il quadro residuo AUTH/ORG e' ora stabile:
  - `ready_create_candidates = 0`
  - `manual_review_candidates = 19`
  - `blocked_candidates = 0`
- La policy prudenziale residua attuale e':
  - i `dealer admin` legacy senza identita' forte restano `review-only`;
  - il principal `mac` non va promosso implicitamente a `organization` target;
  - gli assignment commerciali non possono bypassare la prerequisita `organization_membership`.
- Decisione architetturale corrente sui residui:
  - i `dealer admin` legacy vanno trattati come principal amministrativi da ricondurre in un lane dedicato, non come membership dealer create-only;
  - gli assignment commerciali verso dealer restano validi solo se il commerciale ha una membership minima esplicita sul dealer target;
  - `dealer_operator_assignments` resta quindi un layer sopra la membership, non un sostituto della membership.
- Slice minimo ora applicato per i commerciali usati negli assignment:
  - `dealer_operatore_figura` puo' generare una `organization_membership` minima con `role_code = dealer_operator_member`;
  - l'assignment resta separato e continua a dipendere dalla membership esplicita;
  - sul dataset reale questo riduce i residui review da `19` a `17`, lasciando fuori solo i `dealer admin` legacy e il caso `mac`.
- Secondo passaggio `create-only` AUTH/ORG eseguito sul lane commerciale:
  - committate `2` membership `dealer_operator_member`;
  - committati `2` assignment collegati;
  - al rerun il dataset reale torna a `ready_create = 0`, con `17` review residue stabili.
- Residuo finale corrente del filone AUTH/ORG:
  - `16` principal legacy `dealer.tipo=admin` senza identita' forte;
  - `1` membership `dealer_admin` sul code `mac`, non trattabile finche' `mac` non viene deciso esplicitamente come principal amministrativo o organization target.
- Regola permanente per questo punto:
  - nessun ulteriore widening del gate `create-only` su principal admin legacy;
  - il seguito corretto e' un lane separato di review/reconciliation esplicita, non un auto-import piu' aggressivo.
- Nel lane `dealer admin` legacy e' ora utile distinguere almeno tre sottocategorie:
  - principal backoffice/tecnici o di test, tipicamente `bo_*`, da non promuovere automaticamente a utenti Neo;
  - principal umani o di supporto, come `supporto`, da valutare solo tramite riconciliazione manuale con account Neo reali;
  - principal corporate o istituzionali ambigui, come `mac`, da non reinterpretare automaticamente come organization o user umano.
- Esito operativo raccomandato per ciascuna categoria:
  - principal tecnici/test: `do_not_migrate_automatically`;
  - principal umani/supporto: `manual_link_only_if_real_neo_account_exists`;
  - principal corporate ambigui: `manual_target_decision_required`.
- Decisione target sul caso `mac`:
  - `mac` va trattato come principal amministrativo legacy corporate-ambiguous;
  - non va promosso automaticamente a `organization` Neo;
  - non va promosso automaticamente a `user` Neo;
  - l'eventuale seguito corretto e' solo una riconciliazione manuale esplicita o la scelta di non migrare il record.
- Primo candidato concreto del lane umano/supporto:
  - `supporto` (`dealer.id = 300`, `ragione_sociale = Liberato Malvasi`) e' il miglior candidato a `manual_link_only_if_real_neo_account_exists`;
  - in Neo esiste un utente reale con email `liberato.malvasi@hotmail.com`, ma il naming non e' abbastanza pulito da autorizzare un auto-link;
  - il caso va quindi trattato come `manual_link_candidate`, non come match automatico.
- Short-list minima ora consolidata per i principal umani del lane `dealer admin`:
  - `supporto` => `manual_link_candidate`, perche' esiste un account Neo umano plausibile ma non un segnale abbastanza forte per auto-link;
  - `admin` => `manual_review_only`, perche' il principal e' troppo generico e nel legacy non emerge un'identita' personale affidabile;
  - `rosy` => `manual_review_only`, perche' il naming e' umano ma al momento non esiste un collegamento Neo abbastanza forte per promuoverlo a link candidate;
  - `bo_*` e analoghi => `do_not_migrate_automatically`;
  - `mac` resta fuori da questa short-list perche' appartiene al lane separato `manual_target_decision_required`.
- Refinement ora raccomandato per i lane di candidate classification AUTH/ORG:
  - `internal_platform_principal`
    - sorgente primaria: `dealer.tipo=admin`
    - sottocategorie utili: `superadmin`, `supporto_tecnico`, `backoffice_operativo`, `technical_or_test`, `corporate_ambiguous`
  - `dealer_organization`
    - sorgente primaria: `dealer.tipo=dealer`
  - `dealer_seller`
    - sorgente primaria: `dealer_collaboratore`
    - target iniziale: `users + organization_memberships` dealer-scoped
  - `dealer_operator`
    - sorgente primaria: `commerciali`
    - sottotipi iniziali: `commerciale`, `account`
    - target iniziale: account autenticabile con membership/assignment separati dal lane seller
- Regola pratica derivata dall'audit semantico:
  - il comando import e il reconciliation layer non dovrebbero piu' classificare i candidati solo per tabella sorgente;
  - dovrebbero prima assegnare un lane semantico e solo dopo applicare matching, review o create-only.
- Nota prudenziale sul lane `unknown`:
  - il lane `unknown` deve restare solo come fallback dichiarato per record non ancora classificabili semanticamente;
  - i bridge legacy gia' compresi, come `dealer_user_assignment`, non devono piu' restare in `unknown` e vanno ricondotti al lane corretto prima di qualsiasi decisione di import.
- Slice implementativo ora consolidato:
  - il `dry-run` AUTH/ORG espone `candidate_lane` e `candidate_lane_subtype`;
  - il reconciliation report espone `lane_breakdown`;
  - il lane semantico viene quindi reso visibile prima di qualsiasi decisione su `ready_link`, `ready_create`, `needs_review` o `blocked`;
  - `dealer_user_assignment` viene ora ricondotto esplicitamente al lane `dealer_operator`, riducendo l'area grigia semantica del bootstrap.
- Refinement aggiuntivo ora consolidato:
  - il reconciliation report espone anche `lane_subtype_breakdown`;
  - il lane `internal_platform_principal` puo' quindi essere letto in modo piu' esplicito per sottotipi come `superadmin`, `supporto_tecnico`, `backoffice_operativo`, `technical_or_test`, `corporate_ambiguous`;
  - quando un principal interno resta semantico ma non ancora abbastanza preciso, viene esposto come `unclassified_internal_principal` invece di sparire dal breakdown;
  - questo migliora la leggibilita' dei residui admin/backoffice senza riaprire il gate di commit.
- Refinement operativo ulteriore ora consolidato:
  - il reconciliation report espone anche `internal_principal_category_breakdown`;
  - i residui `internal_platform_principal` possono essere letti subito per categorie operative:
    - `human_plausible`
    - `technical_or_test`
    - `corporate`
  - questa distinzione serve a guidare review e target decision senza allargare il lane automatico.
- Refinement review-oriented ora consolidato:
  - il reconciliation report espone anche `internal_principal_review_action_breakdown`;
  - i principal interni residui vengono letti con esito operativo suggerito:
    - `manual_link_candidate`
    - `manual_review_only`
    - `do_not_migrate_automatically`
    - `manual_target_decision_required`
  - questo consente di trattare il lane residuale come backlog di review governabile, non come massa indistinta.
- Refinement concreto sui casi umani principali ora consolidato:
  - `supporto`
    - resta `manual_link_candidate`;
    - evidenza legacy forte: gruppo `supporto_tecnico`, accesso a `Error Monitor`, `Tools Support`, ACL audit e diagnostica;
    - handling raccomandato: solo link manuale se esiste un account Neo reale verificato, nessun auto-link.
  - `admin`
    - resta `manual_review_only`;
    - evidenza legacy: superadmin/general principal con accesso completo e gestione utenti/dealer/commerciali, ma identita' personale non abbastanza forte;
    - handling raccomandato: nessuna migrazione automatica, nessun link implicito.
  - `rosy`
    - resta `manual_review_only`;
    - evidenza legacy: principal umano plausibile di backoffice operativo, coerente con i flussi di pratiche/approvazione, ma senza segnale forte sufficiente per link candidato;
    - handling raccomandato: review manuale separata, nessun automatismo.

## Note su identity + organization matching heuristics
- Il passo successivo al matcher bootstrap-aware e' chiarire le euristiche di matching, sempre in modalita' read-only.
- Le euristiche devono essere ordinate per affidabilita' e mai trattate come verita' assoluta.
- Prime famiglie di segnali raccomandate:
  - `user`:
    - email/login normalizzato
    - legacy mapping tecnico gia' presente
    - combinazione prudente tra canale e identificativo legacy
  - `organization`:
    - codice dealer/code slug normalizzato
    - legacy mapping tecnico gia' presente
    - nome normalizzato solo come segnale debole, mai come match definitivo da solo
  - `membership`:
    - combinazione di candidate `user` + `organization`
    - ruolo legacy come indizio secondario
    - assignment o join tecniche solo se il significato e' abbastanza stabile
- Regole di prudenza:
  - un solo segnale debole non basta per `matched_existing_target`;
  - segnali in conflitto devono produrre `ambiguous_match`;
  - assenza di segnali affidabili deve produrre `no_existing_match` o `needs_review`.
- Nel workspace esiste ora il contratto tecnico minimo per questo layer:
  - `App\Application\Auth\LegacyImport\Contracts\AuthOrgLegacyMatchingHeuristics`
- E' ora presente anche una prima implementazione concreta:
  - `DefaultAuthOrgLegacyMatchingHeuristics`
  - usata dal matcher read-only auth/org
- Prime euristiche concrete gia' agganciate:
  - `user`: email normalizzata
  - `organization`: code normalizzato
  - `membership`: coppia `user_email + organization_code`
- Il dry-run espone ora anche metriche dedicate alle euristiche:
  - `heuristic_matches`
  - `mapping_matches`

## Note su identity + organization matching edge cases
- Il passo successivo al matching euristico lineare e' chiarire gli edge case.
- Tre famiglie minime da presidiare:
  - segnali ambigui
  - segnali deboli
  - stop conditions
- Esempi di segnali ambigui:
  - piu' target Neo compatibili con lo stesso segnale forte;
  - segnali forti ma confliggenti tra loro;
  - mapping tecnico esistente che punta a un target diverso dal match euristico.
- Esempi di segnali deboli:
  - solo nome normalizzato;
  - ruolo legacy da solo;
  - testo descrittivo non stabile;
  - join tecniche non ancora confermate semanticamente.
- Esempi di stop conditions:
  - piu' match forti incompatibili;
  - target inattivo o semanticamente non compatibile;
  - assenza di segnali sufficienti;
  - conflitto tra mapping tecnico e candidato risolto.
- Regola pratica:
  - edge case forti devono produrre `ambiguous_match` o `needs_review`, non `matched_existing_target`.
- Nel workspace esiste ora il contratto tecnico minimo per questo layer:
  - `App\Application\Auth\LegacyImport\Contracts\AuthOrgLegacyMatchingEdgeCases`
- E' ora presente anche una prima implementazione concreta:
  - `DefaultAuthOrgLegacyMatchingEdgeCases`
  - agganciata al matcher read-only auth/org
- Prime gestioni concrete introdotte:
  - `organization` con solo nome normalizzato => `ambiguous_match`
  - `membership` con pair incompleta => `ambiguous_match`
  - `user` senza segnale sufficiente => `ambiguous_match`
- Il `batch_result` del dry-run va ora in `needs_reconciliation` anche quando emergono `ambiguous_match`, non solo collisioni o unresolved.

## Note su catalog foundation
- Il catalogo va impostato con una gerarchia chiara e verificabile.
- La fondazione indicativa da presidiare e' `fornitore -> compagnia -> prodotto`, salvo esiti diversi di audit futuri.
- L'ordine va confermato tramite audit del legacy e del dominio reale, non dato per scontato oltre quanto gia' emerso.
- L'audit legacy iniziale sul catalogo ha chiarito che oggi il comportamento operativo ruota soprattutto attorno a:
  - `compagnia` come entita' tariffaria/configurativa centrale;
  - `garanzie` usate di fatto come layer prodotto/copertura;
  - `dealer_impostazioni` per abilitazioni runtime di compagnie e dispositivi;
  - tabelle collegate come `compagnia_aree`, `compagnia_coefficienti_anni`, `garanzie_valore`, `garanzie_condizioni`, `dispositivi`.
- Nel legacy non emerge ancora un aggregate catalogo pulito e separato.
- `Prodotti_model` punta direttamente a `garanzie`, segnale utile che il naming storico non va replicato ciecamente nel target Neo.
- La direction `fornitore -> compagnia -> prodotto` resta plausibile come target model, ma il legacy osservato conferma soprattutto la centralita' di `compagnia` e `garanzia/copertura`, non una gerarchia completa gia' pulita.
- Il target model catalogo raccomandato per Neo dovrebbe distinguere almeno tra:
  - `supplier` come sorgente/mandante commerciale o istituzionale del catalogo;
  - `company` come entita' assicurativa/erogatrice con propria configurazione;
  - `product` come offerta o package commerciale riconoscibile;
  - `coverage` come componente di copertura/garanzia associabile al prodotto;
  - `pricing profile` come configurazione tariffaria e territoriale;
  - `dealer enablement` come regola di disponibilita' commerciale, distinta dalla struttura catalogo.
- In questa lettura:
  - `company` non coincide con `product`;
  - `coverage` non coincide automaticamente con `product`;
  - `pricing profile` non dovrebbe essere la stessa cosa del catalogo.
- Nel workspace Neo esiste ora un primo slice tecnico del catalog foundation con persistence minima di:
  - `suppliers`
  - `companies`
  - `products`
  - `coverages`
  - relazione `coverage_product`
- Questo slice serve solo a fissare primitive pulite del modello, non introduce ancora pricing engine, dealer enablement persistito o flussi business.
- E' ora presente anche un primo slice tecnico minimale di `dealer enablement`:
  - relazione `organization_product_enablements`
  - disponibilita' commerciale tra `organization` e `product`
  - `company` derivata dal `product`, senza duplicazione del boundary
- Questo slice non introduce ancora:
  - pricing profile persistito
  - regole territoriali
  - filtri commerciali complessi
  - motore di quotazione
- Il target design successivo sul catalogo deve distinguere anche:
  - `pricing profile` come configurazione di prezzo, durata, territorio, dispositivo e condizioni economiche;
  - `dealer enablement` come disponibilita' commerciale del catalogo per specifici dealer o contesti distributivi.
- `pricing profile` e `dealer enablement` non dovrebbero appartenere allo stesso aggregate:
  - il primo governa come si prezza;
  - il secondo governa cosa e' vendibile o visibile a chi.
- In Neo e' prudente considerare:
  - `pricing profile` agganciato soprattutto a `company`, `product`, `coverage` e varianti contestuali;
  - `dealer enablement` agganciato a `organization/dealer`, `company`, `product` e vincoli commerciali.

## Attenzione a sidebar capability-based
- La sidebar della nuova piattaforma non dovrebbe dipendere solo da ruoli statici o hardcode UI.
- La navigazione dovrebbe riflettere capability, contesto organizzativo e abilitazioni effettive, per sostenere backend-first e multi-client coherence.
- La shell Velzon attuale mostra gia' una prima navigazione alimentata da capability backend condivise via Inertia.
- La base bootstrap attuale usa una versione intenzionalmente minima:
  - capability derivate da ruoli tecnici bootstrap;
  - navigazione filtrata per item tramite `required_capability`;
  - `/api/v1/me` con utente, ruoli bootstrap e capability.
- Il target condiviso successivo dovrebbe evolvere verso:
  - capability names stabili e backend-owned;
  - payload `me` con contesto attivo, membership e scope correnti quando disponibili;
  - navigazione costruita dal backend da capability e stato, non da branching frontend.

## Attenzione a ruoli, permessi e assegnazioni
- Ruoli, permessi e assegnazioni vanno trattati come asset architetturali condivisi, non come dettaglio secondario della UI.
- Serve una distinzione chiara tra autorizzazione, ownership, visibilita' e responsabilita' operativa.
- Un ruolo non deve coincidere automaticamente con un permesso.
- Le capability dovrebbero essere derivabili da membership, assignment, policy e ruolo scoped.
- Nel bootstrap reale sono gia' presenti:
  - `CurrentUserCapabilities` come punto tecnico unico per il calcolo capability;
  - `ResolveAuthenticatedPortalContext` come punto tecnico unico per il contesto autenticato condiviso;
  - `BuildPortalNavigation` come punto tecnico unico per la sidebar;
  - `HandleInertiaRequests` e `/api/v1/me` come punti di consegna dello stato auth condiviso.
- Questi punti vanno evoluti, non sostituiti in modo caotico, nel prossimo step implementativo foundation.

## Attenzione a convivenza legacy / nuova piattaforma
- La nuova piattaforma dovra' convivere per fasi con il legacy.
- Ogni bounded context dovra' esplicitare il proprio livello di autonomia, integrazione e dipendenza residua dal CI3.
- Le decisioni vanno prese per ridurre attrito di migrazione senza compromettere la qualita' del modello target.
- Ogni nuovo bounded context dovra' lasciare memoria stabile di:
  - tabelle o sorgenti legacy lette;
  - strategia di trasformazione verso il target Neo;
  - modalita' di import iniziale o reimport;
  - gap o record che richiedono gestione manuale o regole specifiche.
- La convivenza legacy/new platform non va pensata come replica cieca del CI3 nel nuovo DB:
  - Neo deve nascere con schema pulito;
  - il mapping/import deve garantire transizione e riconciliazione;
  - la business logic nuova non va modellata sulle scorciatoie storiche del legacy.
