# Identity Foundation Target Model

## Contesto
Il workspace Mac Solution Neo dispone gia' di un bootstrap tecnico reale Laravel + Vue, con auth foundation minima, API foundation e shell UI iniziale. Il passo successivo naturale e' chiarire il target model di identity, senza aprire ancora moduli business e senza confondere il bootstrap tecnico con il modello finale.

## Obiettivo del task
Definire un target model iniziale e prudente per `MSN-AUTH-001`, usando il bootstrap reale come base tecnica, chiarendo cosa e' gia' valido come fondazione e cosa invece resta da confermare tramite audit legacy e successivo step organization.

## Stato di partenza osservato nel workspace
- `users` esiste come account autenticabile minimo.
- `roles` e `role_user` esistono come foundation tecnica bootstrap.
- `CurrentUserCapabilities` deriva capability da config e ruoli bootstrap.
- `/api/v1/me` espone dati utente, ruoli bootstrap e capability.
- La shell UI usa gia' capability condivise dal backend per la navigazione iniziale.

## Distinzione esplicita da mantenere
- Comportamento bootstrap attuale:
  - account utente autenticabile;
  - ruoli tecnici bootstrap;
  - capability minime derivate da configurazione.
- Modello target corretto:
  - identity account separata da organization membership;
  - assegnazioni operative scoped;
  - capability derivate da policy e contesto.
- Strategia di transizione:
  - evolvere dall'attuale base tecnica senza replica cieca del legacy;
  - evitare di usare i ruoli bootstrap come modello finale di dominio.

## Target model identity raccomandato
### 1. Identity account
- Un `account` rappresenta il soggetto autenticabile della piattaforma.
- Attributi minimi attesi:
  - identificativo stabile
  - email/login
  - stato attivo/inattivo
  - metadati di sicurezza essenziali
- L'account non deve contenere in modo implicito tutta la semantica organizzativa.

### 2. Persona vs account
- Dove il dominio lo richiedera', la persona e l'account non devono essere assunti come sinonimi assoluti.
- In questa fase e' sufficiente tenere aperta la distinzione concettuale:
  - account = accesso/autenticazione
  - persona = soggetto operativo o anagrafico

### 3. Ruolo
- Un ruolo non deve essere trattato come permesso singolo.
- Il ruolo va inteso come aggregatore o profilo assegnabile, preferibilmente scoped.
- I ruoli bootstrap attuali sono solo tecnici e transitori.

### 4. Capability
- La capability e' il linguaggio corretto per esprimere cosa un account puo' fare.
- La UI non deve hardcodare logica di ruolo di dominio.
- La capability puo' essere derivata da:
  - ruolo scoped
  - membership organizzativa
  - assignment operativo
  - policy backend

### 5. Assignment
- L'assegnazione serve a rappresentare responsabilita' operative contestuali.
- Non coincide con autenticazione.
- Non coincide automaticamente con appartenenza organizzativa.

## Boundary con Organization
- Identity e Organization sono contesti distinti ma fortemente coordinati.
- Identity non dovrebbe modellare l'organizzazione come semplice attributo su `users`.
- Il boundary organization dovra' introdurre almeno:
  - organization
  - membership
  - unita' organizzative o scope equivalenti
  - relazioni di assegnazione e visibilita'

## Implicazioni per backend, API e UI
- Backend:
  - policy e capability devono restare lato server.
  - evitare semantica finale nei ruoli bootstrap correnti.
- API:
  - il payload `/api/v1/me` e' una buona base, ma in futuro dovra' distinguere meglio tra account, membership e scope.
- UI:
  - la shell capability-based attuale e' coerente come direzione.
  - nessuna sidebar definitiva va fissata prima di `MSN-ORG-001` e `MSN-SHARED-001`.

## Cosa non e' stato deciso volutamente in questo step
- modello finale delle entita' organization
- gerarchia completa di unita' organizzative
- mapping legacy dettagliato di ruoli/permessi/assegnazioni
- ACL finale di dominio

## Rischi da evitare
- usare `roles` bootstrap come modello finale
- trattare organization come semplice campo utente
- confondere capability con sola visibilita' UI
- implementare moduli business prima di chiarire membership e assignment

## Risultato dello step
`MSN-AUTH-001` puo' essere considerato chiarito a livello di target model iniziale: l'identity foundation del nuovo portale deve evolvere verso account, capability e assignment distinti, lasciando a `MSN-ORG-001` il completamento del boundary organizzativo.

## Prossimo step consigliato
Aprire `MSN-ORG-001` per definire:
- organization aggregate iniziale
- membership model
- organization unit / scope
- assignment model
- relazione tra ruolo scoped e capability effettive
