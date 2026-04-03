# Contesto
Mac Solution Neo dispone gia' di bootstrap Laravel + Vue, foundation shared iniziale per auth/context/navigation e governance permanente su legacy -> target. Mancava pero' un primo macro-step implementativo reale che consolidasse una persistence minima auth/org piu' leggibile e riusabile.

# Obiettivo del task
Introdurre una persistence minima reale per AUTH/ORG foundation, sufficiente a sostenere:
- account autenticabile;
- membership minima;
- bridge organizzativo minimo;
- assegnazione dealer -> operatore minima;
- shared contract `/api/v1/me` e contesto autenticato allineati a dati persistiti reali.

# Schema target introdotto
- `users`
  - gia' presenti come account autenticabili Neo;
  - stato minimo supportato da `is_active`.
- `organizations`
  - estese con `parent_organization_id` come bridge organizzativo minimo.
- `organization_memberships`
  - gia' presenti come distinzione tra identita' e appartenenza.
- `dealer_operator_assignments`
  - nuova tabella minima per responsabilita' operative dealer -> operatore.

# Motivazione delle scelte
- Non collassare identita', membership e assignment nello stesso record.
- Preparare relazioni tipo `workspace -> dealer` senza aprire l'intero organization domain.
- Rappresentare figure operative come `commerciale/account` tramite `assignment_role_code`, non come ruolo globale rigido o hardcode frontend.
- Lasciare `/api/v1/me` e il context shared leggibili dal backend con dati persistiti veri.

# Cosa e' stato implementato davvero
- Nuovo model:
  - `DealerOperatorAssignment`
- Nuova migration:
  - `organizations.parent_organization_id`
  - tabella `dealer_operator_assignments`
- Relazioni aggiornate su:
  - `Organization`
  - `OrganizationMembership`
  - `User`
- Resolver shared aggiornato:
  - legge `parent_organization`
  - legge `context.assignments`
- Capability shared aggiornate:
  - supporto minimo a capability derivate da assignment
- Seed bootstrap minimo aggiornato:
  - dealer figlio del workspace bootstrap
  - assignment minimo `dealer_account_manager`
- Test API aggiornati e allineati alla nuova persistence minima.

# Cosa e' stato volutamente rimandato
- Organization domain completo
- ACL finale completa
- Assignment model avanzato multi-scope
- Context switching avanzato per assignment
- Dashboard business reali
- Moduli business

# Mapping legacy -> target
- `dealer` legacy:
  - puo' mappare a `organizations` di tipo `dealer` quando rappresenta il soggetto organizzativo/commerciale.
- utenti/admin/operatori legacy:
  - vanno verso `users`, non verso organization.
- appartenenze minime:
  - vanno verso `organization_memberships`.
- assegnazioni dealer -> operatore legacy:
  - vanno verso `dealer_operator_assignments` come responsabilita' operative contestuali.

# Strategia iniziale di import
- Restare inizialmente su dry-run/read-only per auth/org import.
- Usare `legacy_entity_mappings` per tracing tecnico e idempotenza.
- Importare in ordine prudente:
  1. `users`
  2. `organizations`
  3. `organization_memberships`
  4. `dealer_operator_assignments`
- Introdurre commit mode solo dopo output di riconciliazione piu' strutturato.

# Gap e ambiguita' legacy
- Il legacy usa il dealer in modo ambiguo: talvolta principal, talvolta organization.
- Le figure `commerciale/account` non sono ancora sufficientemente pulite per un mapping diretto definitivo.
- Le assegnazioni legacy sono distribuite tra tabelle, helper e sessione.
- Non esiste ancora una semantica unica e definitiva per scoped roles e assignment operativi.

# Perche' questo step prepara bene i successivi
Questa base rende finalmente persistiti e leggibili i tre assi corretti del foundation module:
- identita';
- appartenenza;
- responsabilita' operative minime.

Da qui i prossimi step possono lavorare su:
- riconciliazione import auth/org;
- refining delle capability;
- evoluzione dell'organization model;
senza aprire ancora moduli business.
