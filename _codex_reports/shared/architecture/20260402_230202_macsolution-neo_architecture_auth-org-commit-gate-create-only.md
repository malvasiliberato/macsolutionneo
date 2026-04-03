# Auth/Org Commit Gate Create-Only

## Contesto
Il filone `AUTH/ORG` arriva a questo step dopo:
- reconciliation foundation read-only;
- correction slice sui venditori dealer;
- signal hardening del matcher;
- secondo dry-run reale con esito `GO condizionato`.

Base di partenza del secondo dry-run:
- `records_read = 1621`
- `ready_create_candidates = 1604`
- `manual_review_candidates = 17`
- `blocked_candidates = 0`

I `17` casi residui sono concentrati quasi interamente nei `dealer admin` privi di identita' forte.

## Obiettivo del task
Introdurre una base tecnica reale per un commit gate `create-only` che:
- lavori solo sui candidati `ready_create`;
- escluda esplicitamente review e casi ambigui;
- non faccia merge aggressivi con target Neo gia' esistenti;
- resti idempotente e tracciabile.

## Logica del commit gate create-only
Modalita' introdotta nel comando `legacy:import:auth-org`:
- `--commit-create-only`
- protetta da conferma esplicita `--confirm-create-only`

Regole del gate:
- processare solo righe con `reconciliation_status = ready_create`
- ignorare completamente:
  - `needs_review`
  - `blocked`
  - `matched_existing_target`
- nessun tentativo di merge automatico su target Neo preesistenti
- in caso di conflitto su chiavi uniche o dipendenze mancanti:
  - il candidato viene escluso
  - il motivo viene riportato nel report del gate

## Entita' coinvolte
Perimetro coperto:
- `users`
- `organizations`
- `organization_memberships`
- `dealer_operator_assignments`

Ordine di commit applicato:
1. `organizations`
2. `users`
3. `organization_memberships`
4. `dealer_operator_assignments`

Questo mantiene il gate coerente con le dipendenze del foundation auth/org.

## Esclusioni esplicite
Restano fuori dal primo commit:
- i `17` casi residue in review
- i `dealer admin` senza identita' forte
- ogni candidato con segnali insufficienti
- ogni scenario di merge con target Neo esistenti
- qualunque conflitto non create-only

## Idempotenza e tracing
Il gate usa `legacy_entity_mappings` come base tecnica di tracing:
- nessuna duplicazione se esiste gia' il mapping tecnico coerente;
- mapping salvato con `mapping_status = committed_create_only`;
- rerun idempotente: i candidati gia' committati non vengono ricreati.

## Synthetic identity tecnica
Per rendere persistibili i candidati `ready_create` dealer-scoped senza email legacy valida, il gate introduce una scelta tecnica esplicita e deterministica:
- synthetic email riservata su dominio configurabile
- default:
  - `legacy-auth.macsolution-neo.local`

Uso previsto:
- solo per il create-only gate;
- solo quando il candidato e' gia' classificato `ready_create`;
- nessuna promozione automatica di casi review o admin deboli.

Questo evita di bloccare l'import create-only sui venditori dealer con placeholder email, senza fingere che l'email legacy sia affidabile.

## Cosa e' stato implementato davvero
- nuova classe:
  - `AuthOrgLegacyCreateOnlyCommitGate`
- comando aggiornato:
  - supporto `--commit-create-only`
  - conferma obbligatoria `--confirm-create-only`
- commit gate con:
  - esclusione review/blocked
  - creazione ordinata di organization, user, membership, assignment
  - persistenza mapping tecnica
  - synthetic email deterministica quando necessaria
  - output dedicato con summary + gate snapshot
- dataset controllato dedicato per verifica:
  - `bootstrap-auth-org-create-only`
- test automatici:
  - conferma obbligatoria
  - commit create-only controllato
  - idempotenza al rerun

## Verifica tecnica eseguita
Verifiche eseguite nel workspace:
- `php artisan migrate:fresh --seed`
- `php artisan test`

La suite copre anche il commit gate create-only su dataset controllato con questi esiti:
- `records_scanned = 7`
- `ready_create_candidates = 6`
- `manual_review_candidates = 1`
- `committed_organizations = 1`
- `committed_users = 2`
- `committed_memberships = 2`
- `committed_assignments = 1`
- `mappings_created = 6`
- `synthetic_emails_assigned = 1`

## Cosa e' stato volutamente rimandato
- commit sul dataset legacy reale
- review UI
- merge/matching verso target Neo esistenti
- sync incrementale
- commit di casi ambigui

## Rischi residui
- i `dealer admin` senza identita' forte restano fuori e richiedono regola dedicata futura;
- la synthetic email tecnica risolve la persistenza, non la qualita' semantica del dato legacy;
- il gate non e' ancora pensato per ambienti non controllati senza checklist operativa.

## Condizioni per un futuro commit piu' ampio
Per andare oltre il create-only servono almeno:
- regole esplicite per i `dealer admin` residui;
- review flow o approvazione manuale sui casi ancora ambigui;
- policy chiara per eventuali merge con target Neo preesistenti;
- metriche di match esistente piu' solide.
