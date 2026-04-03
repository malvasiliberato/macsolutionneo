# Contesto
L'audit semantico legacy AUTH/ORG ha chiarito che il legacy non puo' essere letto solo come insieme di tabelle tecniche. Per proseguire bene il filone import, il reconciliation layer deve distinguere gli attori per lane semantico prima di decidere matching, review o create-only.

## Obiettivo del task
Fissare un refinement stabile dei lane di candidate classification AUTH/ORG, coerente con le evidenze legacy gia' raccolte.

## Problema da risolvere
Finora il filone import ha spesso dovuto ragionare su sorgenti tecniche:
- `dealer`
- `dealer_collaboratore`
- `commerciali`

Ma queste sorgenti non coincidono direttamente con i lane target:
- `dealer` contiene sia principal interni sia organization dealer;
- `dealer_collaboratore` contiene seller dealer-scoped;
- `commerciali` contiene operatori autenticabili con portafoglio dealer e policy dedicate.

## Lane semantici fissati
### 1. `internal_platform_principal`
- Sorgente primaria: `dealer.tipo=admin`
- Sottocategorie utili:
  - `superadmin`
  - `supporto_tecnico`
  - `backoffice_operativo`
  - `technical_or_test`
  - `corporate_ambiguous`
- Esempi:
  - `admin`
  - `supporto`
  - `rosy`
  - `bo_*`
  - `mac`

### 2. `dealer_organization`
- Sorgente primaria: `dealer.tipo=dealer`
- Significato:
  - soggetto organizzativo/commerciale
  - non principal interno
  - non seller dealer-side

### 3. `dealer_seller`
- Sorgente primaria: `dealer_collaboratore`
- Significato:
  - utente dealer-side autenticabile
  - membership dealer-scoped
- Target iniziale:
  - `users`
  - `organization_memberships`

### 4. `dealer_operator`
- Sorgente primaria: `commerciali`
- Sottotipi iniziali:
  - `commerciale`
  - `account`
- Significato:
  - operatore autenticabile con portafoglio dealer e policy runtime dedicate
- Target iniziale:
  - account autenticabile + membership/assignment separati dal lane seller

## Regole pratiche per il reconciliation layer
- La candidate classification non dovrebbe piu' partire da `legacy_table -> target_type`.
- Dovrebbe invece seguire questo ordine:
  1. identificare la sorgente tecnica
  2. assegnare il lane semantico
  3. applicare regole di matching/review/commit coerenti col lane

## Implicazioni per l'import AUTH/ORG
- `internal_platform_principal`
  - lane separato dal create-only dealer-side
  - forte spazio di `manual review`, `manual link` o `do_not_migrate_automatically`
- `dealer_organization`
  - lane corretto per create-only organization
- `dealer_seller`
  - lane corretto per create-only dealer-scoped gia' consolidato
- `dealer_operator`
  - lane distinto da seller
  - richiede membership/assignment intenzionali, non inferenze seller-side

## Cosa e' stato aggiornato
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Cosa e' volutamente rimandato
- nessun cambio runtime
- nessun cambio a command/import code
- nessun nuovo commit create-only
- nessuna review UI

## Rischi residui
- senza tradurre questo refinement nel codice del reconciliation layer, i prossimi step rischiano ancora di mescolare lane tecnici e lane semantici
- il lane `internal_platform_principal` richiedera' comunque decisioni manuali su alcuni casi umani o corporate ambigui

## Raccomandazione finale
Il prossimo macro-step corretto e' un `AUTH/ORG reconciliation lane-classification slice`, che introduca nel dry-run e nel report tecnico una classificazione esplicita per lane semantico prima di qualunque ulteriore import o review manuale.
