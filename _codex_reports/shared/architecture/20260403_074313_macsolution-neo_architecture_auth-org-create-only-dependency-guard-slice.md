# Contesto

Nel filone `AUTH/ORG` il primo commit controllato `create-only` e il follow-up successivo avevano isolato un residuo piccolo ma importante:

- `16` casi `dealer_admin_requires_manual_identity_resolution`
- `3` candidati ancora classificati `ready_create`, ma non realmente committabili dal gate per dipendenze mancanti:
  - `1` membership
  - `2` assignment

Questa discrepanza non era un problema di dominio, ma di allineamento tra `dry-run` e capacita' reale del gate `create-only`.

# Obiettivo del task

Riallineare il reconciliation layer `AUTH/ORG` al comportamento reale del gate `create-only`, in modo che il `dry-run` non esponga piu' candidati `ready_create` che il commit non puo' materialmente creare.

# Problema corretto

Prima di questo slice il `dry-run` reale post-commit mostrava ancora:

- `ready_create_candidates = 3`
- `manual_review_candidates = 16`

I tre candidati residui non erano pero' realmente eseguibili:

1. membership `dealer_collaboratore` dealer-scoped, senza dipendenza organizzativa/user realmente risolta
2. assignment `dealer_operatore_figura` con dealer esistente ma membership operatore non presente
3. assignment `dealer_operatore_figura` con lo stesso pattern del punto precedente

In pratica il `dry-run` era piu' ottimistico del gate.

# Regole introdotte

Il reconciliation layer ora applica un secondo controllo di dependency readiness sui candidati inizialmente classificati `ready_create`.

## Membership

Una `membership` resta `ready_create` solo se:

- esiste una dipendenza `user` risolvibile nel batch corrente
- ed esiste una dipendenza `organization` gia' presente oppure risolvibile nel batch corrente

In caso contrario viene riclassificata in `needs_review` con motivi espliciti:

- `membership_candidate_requires_resolved_user_dependency`
- `membership_candidate_requires_resolved_organization_dependency`
- `membership_candidate_requires_resolved_user_and_organization_dependencies`

## Assignment

Un `assignment` resta `ready_create` solo se:

- esiste o viene creato un dealer/organization valido
- ed esiste o viene creato un `organization_membership` coerente dell'operatore

In caso contrario viene riclassificato in `needs_review` con motivi espliciti:

- `assignment_candidate_requires_resolved_membership_dependency`
- `assignment_candidate_requires_resolved_dealer_dependency`
- `assignment_candidate_requires_resolved_membership_and_dealer_dependencies`

# Cosa e' stato implementato davvero

- dependency guard nel `reconciliation report builder`
- breakdown ragioni aggiornato per usare le nuove cause di review
- allineamento del gate snapshot alle stesse ragioni
- dataset di test dedicato ai casi con dipendenze mancanti
- test automatico di regressione sul downgrade da `ready_create` a `needs_review`

# File aggiornati

- `app/Application/Auth/LegacyImport/AuthOrgLegacyReconciliationReportBuilder.php`
- `app/Application/Auth/LegacyImport/AuthOrgLegacyCreateOnlyCommitGate.php`
- `app/Application/Auth/LegacyImport/AuthOrgLegacyDryRun.php`
- `tests/Feature/Console/AuthOrgLegacyImportCommandTest.php`
- `MASTER_PROGRESS.md`

# Esito sul dataset reale

Dry-run reale rieseguito su `legacy-ci3-auth-org` / `sql1483615_1`.

## Prima del slice

- `records_read = 1621`
- `ready_create_candidates = 3`
- `manual_review_candidates = 16`
- `blocked_candidates = 0`

## Dopo il slice

- `records_read = 1621`
- `ready_link_candidates = 1602`
- `ready_create_candidates = 0`
- `manual_review_candidates = 19`
- `blocked_candidates = 0`

## Breakdown review reale aggiornato

- `dealer_admin_requires_manual_identity_resolution = 16`
- `membership_candidate_requires_resolved_organization_dependency = 1`
- `assignment_candidate_requires_resolved_membership_dependency = 2`

# Perche' questo slice e' utile

Questo non allarga il perimetro del commit.

Al contrario:

- elimina falsi positivi dal `dry-run`
- rende il reporting coerente con il gate `create-only`
- separa chiaramente i residui reali dai candidati realmente committabili
- prepara un eventuale follow-up mirato solo su review tecniche residue

# Cosa e' stato volutamente rimandato

- nessun nuovo commit sul dataset reale
- nessun merge con target Neo esistenti
- nessuna UI di review
- nessuna promozione automatica dei `dealer admin`
- nessuna risoluzione aggressiva di membership/assignment mancanti

# Rischi residui

- i `dealer admin` restano il nucleo principale di ambiguita' identitaria
- i `2` assignment residui dipendono ancora da membership non risolte nel perimetro prudente attuale
- la membership residua con bridge organizzativo insufficiente richiede una decisione esplicita, non una scorciatoia tecnica

# Raccomandazione operativa finale

Il filone `AUTH/ORG` e' ora piu' pulito e affidabile dal punto di vista reconciliation.

Il prossimo passo corretto non e' un nuovo widening del gate, ma una review tecnica mirata dei `19` residui reali:

- `16` dealer admin
- `1` membership senza bridge organizzativo sufficiente
- `2` assignment senza membership prerequisita
