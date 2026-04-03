# Contesto

Dopo il lane decision su:

- `dealer admin` legacy come principal amministrativi review-only
- assignment commerciali subordinati a membership esplicita

restava un piccolo problema pratico:

i `2` assignment commerciali residuali erano semanticamente corretti, ma non trattabili nel `create-only` perche' il reconciliation layer non produceva ancora una membership minima esplicita per quegli operatori.

# Obiettivo del task

Introdurre il minimo necessario per rappresentare, nel lane import/reconciliation `AUTH/ORG`, una membership dealer-scoped esplicita dei commerciali usati negli assignment, senza toccare:

- il lane `dealer admin`
- il gate `create-only`
- il dominio organization in modo esteso

# Scelta adottata

Da `dealer_operatore_figura` il reconciliation layer puo' ora produrre anche:

- una `organization_membership` minima con `role_code = dealer_operator_member`

in aggiunta all'assignment vero e proprio.

Questo mantiene la separazione corretta:

- `membership` = prerequisito di appartenenza minima al dealer
- `assignment` = responsabilita' operativa sopra quella membership

# Motivazione

Questa e' la soluzione piu' prudente perche':

- non usa l'assignment come scorciatoia per rappresentare appartenenza
- non inventa organization nuove
- non promuove implicitamente i `dealer admin`
- non richiede schema nuovo: `organization_memberships.role_code` e' gia' sufficiente

# Implementazione reale

## Dataset adapter

`MySqlAuthOrgLegacyDatasetAdapter` ora emette, per `dealer_operatore_figura`:

- un candidato `membership`
- un candidato `assignment`

La membership minima usa:

- `candidate = membership`
- `target_membership_role_code = dealer_operator_member`
- chiave composta da `dealer_code + operator_id + membership_role_code`

## Capability map

`config/portal.php` ora riconosce `dealer_operator_member` come membership role foundation con:

- `organization.context.read`

## Dataset controllati

I dataset bootstrap sono stati riallineati per:

- coprire il nuovo lane membership+assignment
- verificare che il guard sui prerequisiti continui a funzionare

# Esito sul dataset reale

Dry-run reale rieseguito su `sql1483615_1`.

## Prima del slice

- `ready_create_candidates = 0`
- `manual_review_candidates = 19`

## Dopo il slice

- `records_read = 1623`
- `ready_create_candidates = 4`
- `manual_review_candidates = 17`
- `blocked_candidates = 0`

## Breakdown aggiornato

- `membership.ready_create = 2`
- `assignment.ready_create = 2`
- review residue:
  - `16` `dealer_admin_requires_manual_identity_resolution`
  - `1` `membership_candidate_requires_resolved_organization_dependency`

Quindi il lane commerciale e' ora sbloccato senza toccare il lane `dealer admin`.

# Cosa e' stato volutamente rimandato

- nessun secondo commit `create-only` in questo slice
- nessuna scrittura sul dataset reale
- nessuna promozione dei `dealer admin`
- nessuna ridefinizione estesa del modello organization

# File aggiornati

- `config/portal.php`
- `app/Application/Auth/LegacyImport/MySqlAuthOrgLegacyDatasetAdapter.php`
- `app/Application/Auth/LegacyImport/AuthOrgLegacyDryRun.php`
- `tests/Feature/Console/AuthOrgLegacyImportCommandTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# Verifiche

- `php artisan test --filter=AuthOrgLegacyImportCommandTest` -> verde
- `php artisan legacy:import:auth-org --dry-run --dataset=legacy-ci3-auth-org --batch=5000` rieseguito sul dataset reale

# Raccomandazione operativa finale

Il prossimo macro-step corretto e' un secondo passaggio `create-only` AUTH/ORG limitato ai candidati appena sbloccati dal lane commerciale:

- `2` membership `dealer_operator_member`
- `2` assignment collegati

lasciando invariati fuori perimetro:

- i `16` `dealer admin`
- il caso `mac`
