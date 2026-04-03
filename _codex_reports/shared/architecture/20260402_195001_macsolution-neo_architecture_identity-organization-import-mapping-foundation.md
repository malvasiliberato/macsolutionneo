# Identity + Organization Import Mapping Foundation

## Contesto
Mac Solution Neo dispone gia' di:
- target model iniziale di identity;
- target model iniziale di organization;
- audit legacy su identity + organization;
- governance permanente del filone import legacy -> Neo.

Il primo bounded context su cui applicare questa governance e' `Identity + Organization`, perche' influenza autenticazione, appartenenza organizzativa, assignment e capability di tutti i moduli successivi.

## Obiettivo del task
Definire il primo pacchetto esplicito e riusabile per `MSN-AUTH-004`, distinguendo:
- schema target;
- mapping legacy -> target;
- strategia di import;
- gap, collisioni e casi non mappabili automaticamente.

## Schema target minimo di riferimento
- `users`
  - account autenticabile Neo
- `organizations`
  - aggregate organizzativo esplicito
- `organization_memberships`
  - legame account -> organization
- layer successivi da non comprimere ora nel primo import:
  - ruoli scoped
  - capability effettive
  - assignment operativi

## Mapping legacy -> target raccomandato

### 1. Principali legacy osservati
- backoffice `admin` autenticato su record `dealer` con `tipo = admin`
- area `dealer` con sessione distinta e principal di fatto non unificato
- API JWT dealer come canale tecnico separato
- figure operative `commerciale` e `account`
- assignment dealer/commerciali distribuiti su tabelle dedicate e sessione

### 2. Mapping iniziale prudente
- identita' legacy candidate -> `users`
  - i record legacy che rappresentano soggetti autenticabili vanno riletti come candidati `account`, non copiati per tipo canale.
- dealer legacy -> `organizations` oppure sorgente da riconciliare
  - quando il dealer rappresenta contesto commerciale o organizzativo, va considerato candidato `organization`, non account.
  - quando il dealer viene usato tecnicamente come principal login, serve riconciliazione esplicita e non mapping diretto automatico.
- legami utente/dealer -> `organization_memberships`
  - le relazioni storiche che esprimono appartenenza o operativita' continuativa verso dealer sono candidate a membership, da distinguere dagli assignment puntuali.
- figure `commerciale` e `account` -> futuri assignment
  - non vanno schiacciate nel primo import account/membership.
- ACL legacy -> sorgente di audit e mapping permissionale
  - utile per futura capability resolution, non per import uno-a-uno nel primo slice.

## Strategia di import raccomandata

### Fase 1. Inventory e riconciliazione identita'
- identificare i record legacy che rappresentano davvero soggetti autenticabili
- separare gli accessi tecnici di canale dai soggetti di dominio
- produrre mapping candidate tra principal legacy e `users`

### Fase 2. Organization bootstrap import
- identificare i dealer o altri record legacy che nel dominio Neo devono diventare `organizations`
- evitare mapping automatici dove il dealer legacy e' semanticamente ambiguo

### Fase 3. Membership construction
- costruire `organization_memberships` solo quando esiste una relazione di appartenenza o operativita' continuativa sufficientemente chiara
- non usare ancora gli assignment commerciali o account come membership se il significato operativo non e' stabile

### Fase 4. Layer successivi
- ruoli scoped
- capability
- assignment
- eventuale import ACL reinterpretata

## Tracking legacy references raccomandato
- `users`
  - evitare in prima battuta troppi campi `legacy_*` diretti se la provenienza e' multi-sorgente o ambigua
- `organizations`
  - `legacy_id` diretto e' accettabile solo quando l'equivalenza col dealer legacy e' semplice e stabile
- `organization_memberships`
  - preferire mapping table tecniche dedicate quando la membership deriva da piu' indizi o da join storiche non lineari
- generale
  - usare mapping table dedicate quando serve reimport idempotente o audit tecnico della riconciliazione

## Gap, collisioni e ambiguita' gia' emersi
- il principal legacy non e' unificato tra admin, dealer e API
- il dealer legacy puo' significare account tecnico, organizzazione o contesto operativo
- i ruoli storici non sono abbastanza puliti da diventare ruoli target senza reinterpretazione
- le figure operative e gli assignment sono distribuiti tra tabelle, helper runtime e sessione
- alcuni match account <-> organization richiederanno riconciliazione manuale o regole specifiche

## Cosa e' stato volutamente rimandato
- import completo di utenti legacy
- import completo dealer/commerciali/account
- persistence tecnica definitiva delle mapping table
- comandi artisan di import reali
- sincronizzazione transitoria o bidirezionale

## Risultato finale
`MSN-AUTH-004` puo' considerarsi validato come primo pacchetto concreto di import-governance applicato a un bounded context reale. Il workspace ora conserva una traccia esplicita di:
- schema target auth/org;
- mapping legacy -> target raccomandato;
- strategia di import per fasi;
- gap e collisioni da non nascondere.

## Perche' questo prepara bene i prossimi step
- evita che `Identity + Organization` venga implementato scollegato dai dati reali del legacy
- prepara il design della persistence tecnica delle legacy references
- fornisce un metodo subito riapplicabile a catalogo e agli altri bounded context

## Prossimo step consigliato
Aprire `MSN-AUTH-005` per chiarire dove usare:
- mapping table dedicate
- `legacy_id` semplici
- tracking tecnico esterno al dominio

con focus su `users`, `organizations` e `organization_memberships`.
