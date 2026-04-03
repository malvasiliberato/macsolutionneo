# Mac Solution Neo - Capability and Sidebar Governance

## Contesto
Il bootstrap reale del portale Neo e' gia' operativo in Laravel + Vue con shell autenticata, `/api/v1/me`, capability minime e navigazione filtrata dal backend. Dopo l'audit legacy su identity + organization, serviva chiarire il contratto shared che deve collegare autenticazione, capability, sidebar e contesto utente senza introdurre ancora moduli business.

## Obiettivo del task
Definire `MSN-SHARED-001` come design shared del nuovo workspace, chiarendo:
- come devono essere trattate le capability;
- quale ruolo deve avere `/api/v1/me`;
- come la sidebar deve derivare dal backend;
- come preparare scope e context futuri senza rompere il bootstrap attuale.

## Stato attuale osservato nel bootstrap reale
- `CurrentUserCapabilities` calcola capability a partire da:
  - capability base da config;
  - mapping ruolo tecnico bootstrap -> capability.
- `BuildPortalNavigation` costruisce la navigazione filtrando item tramite `required_capability`.
- `HandleInertiaRequests` distribuisce al frontend:
  - `auth.user`
  - `auth.capabilities`
  - `portal.navigation`
  - `portal.boundedContexts`
- `/api/v1/me` restituisce:
  - dati base utente
  - ruoli bootstrap
  - capability correnti

## Limiti attuali volutamente accettati
- Le capability dipendono ancora da ruoli tecnici bootstrap.
- La navigazione usa una regola semplice uno-item / una-capability.
- Il payload `me` non espone ancora organization context, membership o scope attivo.
- Non esiste ancora distinzione runtime tra:
  - capability globali
  - capability contestuali
  - assignment operativi

## Decisioni di governance adottate

### 1. Le capability restano l'unica superficie condivisa verso UI
- La UI non deve ragionare per ruoli di dominio hardcoded.
- Il frontend deve ricevere capability gia' calcolate dal backend.
- I ruoli restano informazione tecnica o diagnostica, non contratto primario della UI.

### 2. `/api/v1/me` diventa il payload canonico iniziale del contesto autenticato
- Il contratto `me` deve essere il punto stabile da cui derivare:
  - identita' autenticata;
  - capability effettive;
  - contesto attivo;
  - metadata minimi utili alla UI.
- La condivisione Inertia e il payload API devono restare semanticamente allineati.
- In futuro Inertia potra' essere alimentata dallo stesso contract model senza duplicazioni semantiche.

### 3. La sidebar deve essere backend-driven
- La sidebar non e' un semplice dettaglio grafico.
- Gli item di navigazione devono derivare da:
  - capability effettive;
  - stato del workspace o del contesto;
  - disponibilita' reale dei moduli.
- Il frontend deve limitarsi a renderizzare item gia' ammessi dal backend.

### 4. Il modello shared deve gia' distinguere global vs contextual
- Alcune capability possono restare globali:
  - accesso portale
  - gestione profilo
- Altre capability dovranno essere contestuali:
  - scope organization attivo
  - membership corrente
  - assignment specifici
- Questa distinzione deve entrare nel design prima della persistence completa.

## Contract target raccomandato

### Payload `me` target evolutivo
Il payload target dovrebbe poter convergere almeno verso questi blocchi:
- `user`
  - identity account di base
- `capabilities`
  - elenco capability effettive gia' risolte
- `context`
  - organization attiva quando disponibile
  - membership attiva quando disponibile
  - scope attivo quando disponibile
- `roles`
  - ruoli tecnici o scoped esposti solo come supporto, non come contratto primario UI

### Navigation contract target
Ogni item di navigazione dovrebbe poter evolvere almeno con:
- `key`
- `label`
- `route`
- `description`
- `state`
  - disponibile
  - coming_soon
  - hidden
- `context_requirements`
  - eventuale necessity di membership o scope attivo

Il bootstrap attuale puo' restare su un subset minimale di questo contratto.

## Implicazioni per il prossimo step implementativo
- Non serve ancora introdurre ACL finale o moduli business.
- Serve introdurre un primo slice tecnico che prepari:
  - contesto attivo shared
  - shape piu' stabile di `me`
  - evoluzione non distruttiva di `CurrentUserCapabilities`
  - evoluzione non distruttiva di `BuildPortalNavigation`

## Cosa e' stato volutamente rimandato
- tassonomia finale di tutte le capability di dominio
- organization persistence completa
- assignment model completo
- sidebar definitiva modulo per modulo
- permessi granulari di catalogo, preventivi, pratiche, documenti, finanziamenti e firma

## Rischi da evitare
- usare ancora il codice ruolo come shortcut di dominio nel frontend
- far divergere troppo i contract Inertia e API
- introdurre sidebar frontend-driven con logica locale
- aggiungere subito tabelle definitive auth/org senza un primo slice shared minimo e coerente

## Risultato finale
`MSN-SHARED-001` puo' considerarsi chiarito a livello design:
- capability restano backend-owned;
- `/api/v1/me` e' il contract canonico iniziale del contesto autenticato;
- la sidebar deve essere derivata dal backend;
- il prossimo passo corretto e' un piccolo step implementativo foundation, non un modulo business.

## Step successivo consigliato
Aprire `MSN-AUTH-002` come primo slice implementativo shared auth/org con focus su:
- shape condivisa del contesto autenticato
- preparazione di `context` nel payload `me`
- refactor minimo e non distruttivo di capability/navigation per accogliere scope e membership futuri
