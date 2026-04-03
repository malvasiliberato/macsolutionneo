# Auth/Org Post Create-Only Reconciliation Follow-Up

## Contesto
Dopo il primo commit controllato `create-only` su `sql1483615_1`, il filone `AUTH/ORG` ha prodotto:
- `654` users creati
- `272` organizations create
- `425` memberships create
- `1` assignment creato
- `1351` mapping tecnici `committed_create_only`

Il commit ha escluso correttamente i casi `needs_review` e ha mantenuto fuori i `dealer admin` senza identita' forte.

## Obiettivo del follow-up
Consolidare i residui del primo passaggio create-only, distinguendo chiaramente:
- review residue vere
- conflitti unici osservati nel primo passaggio
- candidati ancora `ready_create` ma non committati per dipendenze mancanti al rerun

## Residui effettivi osservati
### Review residue
Classificazione attuale sul dataset reale dopo il primo commit:
- `review_count = 16`
- `review_reason = dealer_admin_requires_manual_identity_resolution`

Questi 16 casi corrispondono a record `dealer.tipo = admin` senza identita' forte, con `username` presente ma senza email affidabile.

Esempi di username osservati:
- `admin`
- `mac`
- `supporto`
- `rosy`
- vari account di test/backoffice come `bo_fin_test_01`, `bo_ui_step66`, `bo_pwd_step78`

### Ready-create residui al rerun
Al rerun del gate create-only:
- `ready_count = 3`
- breakdown:
  - `membership = 1`
  - `assignment = 2`

Questi candidati non sono stati creati al rerun per dipendenze mancanti:
- `membership_missing_user_or_organization_dependency = 1`
- `assignment_missing_membership_or_dealer_dependency = 2`

### Conflitti osservati nel primo passaggio
Runtime exclusions registrate nel primo passaggio:
- `organization_unique_key_conflict = 1`
- `user_unique_key_conflict = 11`
- `membership_missing_user_or_organization_dependency = 1`
- `assignment_missing_membership_or_dealer_dependency = 2`

## Evidenze legacy ancora rilevanti
- `dealer admin` legacy totali: `16`
- tutti i `dealer admin` residui sono ancora privi di identita' forte sufficiente per il create-only automatico
- `dealer_collaboratore` con email non affidabile o placeholder: `508`
- gruppi di email duplicate tra i collaboratori: `9`
- gruppi di username duplicate nella tabella `dealer`: `2`

## Valutazione del follow-up
Il primo commit create-only e' riuscito e il sistema si comporta in modo sostanzialmente stabile al rerun.

La situazione residua e' ora molto piu' governabile:
- il problema principale non e' piu' il volume complessivo del dataset;
- il problema e' concentrato in un set piccolo e semanticamente chiaro;
- i `dealer admin` restano il vero boundary aperto.

## Lettura architetturale
Il filone `AUTH/ORG` ha ora:
- un perimetro create-only gia' operativo per dealer organization, dealer seller e memberships trattabili;
- un set ristretto di principal amministrativi legacy che richiede regola dedicata o review flow;
- residui tecnici minori su dipendenze mancanti tra membership e assignment.

## Raccomandazione finale
Prossimo slice raccomandato:
- `dealer admin reconciliation slice`

Focus:
- chiarire se i `dealer admin` vanno:
  - esclusi definitivamente dal primo import,
  - trasformati in account tecnici separati,
  - oppure gestiti con una regola manuale/assistita

Sub-focus tecnico secondario:
- chiudere i `3` residui create-only dipendenti da membership/assignment mancanti, senza riaprire il modello.
