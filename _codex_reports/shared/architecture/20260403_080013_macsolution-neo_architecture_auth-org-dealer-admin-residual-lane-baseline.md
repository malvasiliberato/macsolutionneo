# Contesto

Il filone `AUTH/ORG` ha ormai chiuso:

- il lane `dealer organization`
- il lane `dealer seller`
- il lane commerciale con membership minima + assignment

Dopo il secondo passaggio `create-only` sul lane commerciale, il dataset reale non presenta piu' candidati `ready_create`.

Restano `17` review residue.

# Obiettivo del task

Consolidare la baseline finale dei residui `AUTH/ORG` rimasti, cosi' da chiudere il filone automatico e aprire eventualmente un lane separato e piu' esplicito per i principal admin legacy.

# Esito residuo finale

## Conteggio

- `review_count = 17`

## Breakdown motivi

- `dealer_admin_requires_manual_identity_resolution = 16`
- `membership_candidate_requires_resolved_organization_dependency = 1`

# Natura dei residui

## 1. Principal `dealer.tipo=admin`

I `16` casi principali sono tutti:

- `legacy_table = dealer`
- `tipo = admin`
- `username/code` presenti
- nessuna identita' forte affidabile

Esempi:

- `admin`
- `mac`
- `bo_fin_test_01`
- `bo_operatore_01`
- `supporto`
- `rosy`

Questi record non sono piu' un problema di matching o gate.

Sono un problema di decisione target:

- lane amministrativo dedicato?
- principal interni da riconciliare manualmente?
- soggetti da non importare in Neo?

## 2. Caso `mac`

Il residuo finale non-user e':

- `candidate_type = membership`
- `legacy_table = dealer_collaboratore`
- `legacy_organization_code = mac`
- `target_membership_role_code = dealer_admin`

Questo caso resta aperto perche':

- `mac` oggi emerge come principal admin legacy
- non esiste come `organization` target nel Neo locale
- promuoverlo automaticamente a organization sarebbe un widening non giustificato

# Decisione consolidata

Il filone automatico `create-only` si considera chiuso.

Da qui in poi:

- nessun nuovo widening del gate su principal admin legacy
- nessuna synthetic identity per i `dealer admin`
- nessuna organization implicita per `mac`

Il prossimo seguito corretto non e' piu' un import slice automatico, ma un lane dedicato di review/reconciliation esplicita.

# Cosa e' stato lasciato invariato volutamente

- nessuna nuova scrittura sul dominio Neo
- nessun nuovo commit `create-only`
- nessuna UI di review
- nessun merge o import aggressivo sui `dealer admin`

# File aggiornati

- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# Raccomandazione operativa finale

Aprire, solo se serve davvero, un lane separato:

- `AUTH/ORG dealer-admin reconciliation lane`

con obiettivo stretto:

- decidere il target dei principal admin legacy
- chiarire se `mac` e' un principal amministrativo, una pseudo-organization storica o un record da non migrare automaticamente
