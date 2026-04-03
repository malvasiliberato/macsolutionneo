# Contesto

Dopo:

- `AUTH/ORG` reconciliation foundation
- signal hardening
- secondo dry-run reale
- commit gate `create-only`
- primo commit controllato reale
- dependency guard sul reconciliation layer

il filone `AUTH/ORG` non ha piu' candidati `ready_create` residui nel dataset reale `sql1483615_1`.

Restano pero' `19` casi in `needs_review` che costituiscono il perimetro reale del follow-up.

# Obiettivo del task

Consolidare una baseline leggibile e stabile dei residui `AUTH/ORG` post-`create-only`, in modo che il prossimo step possa lavorare su:

- `dealer admin` legacy
- un caso di membership dipendente da un dealer non importato come organization
- due assignment commerciali senza membership prerequisita

senza riaprire il resto del filone.

# Stato rilevato

Dry-run reale rieseguito in sola lettura sul dataset `legacy-ci3-auth-org`.

## Quadro complessivo

- `records_read = 1621`
- `ready_link_candidates = 1602`
- `ready_create_candidates = 0`
- `manual_review_candidates = 19`
- `blocked_candidates = 0`

## Breakdown per entita'

- `user = 16`
- `membership = 1`
- `assignment = 2`

## Breakdown per motivo

- `dealer_admin_requires_manual_identity_resolution = 16`
- `membership_candidate_requires_resolved_organization_dependency = 1`
- `assignment_candidate_requires_resolved_membership_dependency = 2`

# Evidenze residue consolidate

## 1. Dealer admin legacy

I `16` casi review principali corrispondono a principal legacy `dealer.tipo=admin`, con:

- username/code presenti
- email assente
- identita' forte non verificabile automaticamente

Esempi osservati:

- `admin`
- `mac`
- `bo_fin_test_01`
- `bo_operatore_01`
- `supporto`
- `rosy`

Questi soggetti non sono oggi trattabili come `create-only` senza introdurre un'identita' artificiale non sufficientemente giustificata.

## 2. Membership residua

Esiste `1` membership review:

- `legacy_table = dealer_collaboratore`
- `legacy_organization_code = mac`
- `legacy_user_username = mac`
- `target_membership_role_code = dealer_admin`

L'evidenza pratica e' che `mac` emerge nel legacy come principal `dealer admin`, ma non come organization dealer autonoma gia' importata nel perimetro prudente corrente.

Quindi la membership non puo' essere promossa automaticamente senza una decisione esplicita sul significato target di `mac`.

## 3. Assignment residui

Esistono `2` assignment review:

- operatore: `liberato.malvasi@hotmail.com`
- dealer target:
  - `Macsolution1980@`
  - `m.car`
- ruolo assignment: `commerciale`

Le organization dealer esistono in Neo, l'user del commerciale esiste, ma manca una membership dealer-scoped coerente che permetta di creare l'assignment in modo prudente e idempotente.

Quindi il problema non e' il dealer, ma il bridge `user -> organization_membership` richiesto come prerequisito tecnico per l'assignment.

# Interpretazione architetturale

Questa baseline conferma che il problema residuo non e' piu' di volume, ma di semantica e prerequisiti:

- i `dealer admin` sono principal legacy ambigui, non semplici seller dealer-scoped
- il caso `mac` mette in evidenza la distinzione tra principal amministrativo e organization target
- gli assignment commerciali richiedono una membership esplicita, non scorciatoie tecniche

# Cosa e' stato lasciato invariato volutamente

- nessuna nuova scrittura nel dominio Neo
- nessun widening del gate `create-only`
- nessun merge su target esistenti
- nessuna promozione automatica dei `dealer admin`
- nessuna generazione artificiale di membership per chiudere i `2` assignment

# Perche' questa baseline e' utile

Il prossimo macro-step puo' ora essere piccolo e leggibile:

1. decidere la policy prudenziale sui `dealer admin`
2. chiarire il caso `mac`
3. decidere se gli assignment commerciali richiedono una membership tecnica dedicata oppure una diversa regola target

Questo evita di rientrare nel matcher generale o nel commit gate senza un problema reale da risolvere.

# Raccomandazione operativa finale

Aprire un `AUTH/ORG residual review slice` con tre uscite esplicite:

- policy target per `dealer admin`
- decisione sul mapping target del caso `mac`
- regola minima per consentire o escludere gli assignment commerciali senza membership dealer-scoped prerequisita
