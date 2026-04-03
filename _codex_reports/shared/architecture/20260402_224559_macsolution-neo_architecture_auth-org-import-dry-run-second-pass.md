# Auth/Org Import Dry-Run Second Pass

## Contesto
Nel filone `AUTH/ORG` erano gia' presenti:
- correction slice sui venditori dealer come attori autenticabili di primo livello;
- reconciliation foundation read-only;
- primo dry-run reale con esito `NO-GO`;
- signal hardening del reconciliation layer su placeholder identity, venditori dealer-scoped, dealer admin e membership pairing.

## Obiettivo del task
Rieseguire il dry-run reale sul dataset legacy `sql1483615_1` usando il layer di signal hardening, confrontare le metriche col primo run e capire se esiste ora un `GO`, `GO condizionato` o `NO-GO` verso un futuro commit controllato.

## Dataset / sorgenti legacy usate
- database legacy reale: `sql1483615_1`
- sorgenti lette:
  - `dealer`
  - `dealer_collaboratore`
  - `commerciali`
  - `dealer_operatore_figura`

## Comandi / modalita' di esecuzione
```powershell
$env:LEGACY_IMPORT_DB_CONNECTION='legacy_mysql'
$env:LEGACY_IMPORT_DB_HOST='localhost'
$env:LEGACY_IMPORT_DB_PORT='3306'
$env:LEGACY_IMPORT_DB_DATABASE='sql1483615_1'
$env:LEGACY_IMPORT_DB_USERNAME='root'
$env:LEGACY_IMPORT_DB_PASSWORD=''
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=legacy-ci3-auth-org --batch=5000
```

Approfondimenti mirati eseguiti anche su:
```powershell
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=dealer_collaboratore --dataset=legacy-ci3-auth-org --batch=5000
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=dealer --dataset=legacy-ci3-auth-org --batch=5000
```

## Confronto primo run vs secondo run
### Metriche aggregate
| Metrica | Primo run | Secondo run | Delta |
| --- | --- | --- | --- |
| `records_read` | `1621` | `1621` | `0` |
| `ready_create_candidates` | `595` | `1604` | `+1009` |
| `manual_review_candidates` | `1026` | `17` | `-1009` |
| `blocked_candidates` | `0` | `0` | `0` |
| `ambiguous_matches` | `1026` | `17` | `-1009` |
| `matched_existing_targets` | `0` | `0` | `0` |
| `high_confidence_matches` | `0` | `0` | `0` |

### Lettura del delta
Il miglioramento e' sostanziale, non marginale:
- il volume totale e' invariato;
- la quasi totalita' dei casi prima classificati come review/ambiguous e' stata riclassificata in `ready_create` prudenziale;
- non e' ancora aumentata la quota di match esistenti o ad alta confidenza, perche' il dataset Neo non ha ancora target gia' riconciliati in volume.

## Metriche aggregate del secondo run
- `records_read = 1621`
- `user_candidates = 681`
- `organization_candidates = 274`
- `membership_candidates = 664`
- `assignment_candidates = 2`
- `matched_existing_targets = 0`
- `unmatched_candidates = 1604`
- `ambiguous_matches = 17`
- `high_confidence_matches = 0`
- `medium_confidence_matches = 0`
- `low_confidence_matches = 17`
- `no_confidence_matches = 1604`
- `ready_create_candidates = 1604`
- `manual_review_candidates = 17`
- `blocked_candidates = 0`
- `batch_result = needs_reconciliation`

## Breakdown per entita'
### Secondo run
- `user`
  - `ready_create = 664`
  - `needs_review = 17`
  - `blocked = 0`
- `organization`
  - `ready_create = 274`
  - `needs_review = 0`
  - `blocked = 0`
- `membership`
  - `ready_create = 664`
  - `needs_review = 0`
  - `blocked = 0`
- `assignment`
  - `ready_create = 2`
  - `needs_review = 0`
  - `blocked = 0`

### Effetto pratico del signal hardening
- `dealer_collaboratore`
  - secondo run mirato: `1328` record, tutti riclassificati in `ready_create`
  - lettura: il pairing prudenziale `dealer_code + username` ha ridotto quasi totalmente i falsi ambigui sui venditori dealer-scoped
- `dealer`
  - run mirato: `290` record
  - `274` organization candidate `ready_create`
  - `16` user candidate `needs_review`
  - lettura: il caso residuale e' ormai concentrato sui `dealer admin`

## Evidenze sui casi residui
- `review_reason_breakdown`
  - `dealer_admin_requires_manual_identity_resolution = 16`
  - `user_candidate_has_insufficient_signals = 1`
- evidenze legacy ancora rilevanti:
  - `508` righe in `dealer_collaboratore` con email non affidabile o placeholder
  - `9` gruppi di email duplicate nei collaboratori
  - `2` username duplicate nella tabella `dealer`
  - la tabella `dealer` reale non espone un campo `email` stabile; per i `dealer admin` il segnale identitario resta quindi strutturalmente debole

## Casi speciali
### Venditori dealer-scoped
Il miglioramento maggiore e' qui:
- prima: molti casi finivano in `needs_review` per email mancanti o placeholder
- dopo: il layer usa in modo prudente `dealer_code + username`, senza promuovere i candidati a match esistenti ma rendendoli trattabili come `ready_create`

### Dealer admin
Resta il punto piu' delicato:
- `16` record legacy `dealer.tipo = admin`
- nessuno con segnale email stabile
- tutti restano in `needs_review`
- questo e' coerente con la regola prudenziale introdotta: niente promozione automatica di principal amministrativi senza identita' forte

## Eventuali micro-fix applicati
Nessun micro-fix applicativo aggiuntivo in questo task.

Il secondo run e' stato eseguito con il reconciliation layer gia' hardenizzato; il lavoro qui e' di validazione comparativa e classificazione.

## Valutazione complessiva della readiness al commit futuro
Stato consigliato: `GO condizionato`

Motivazione:
- il miglioramento del dry-run e' sostanziale;
- i casi residui sono pochi e concentrati;
- il rischio e' ora piu' governabile;
- ma non e' ancora prudente un commit indiscriminato su tutto il perimetro `AUTH/ORG`.

## Raccomandazione finale
`GO condizionato`

Passo successivo raccomandato:
- aprire il commit gate design in modalita' create-only;
- escludere esplicitamente i `17` casi residue da review;
- mantenere fuori dal primo commit:
  - tutti i `dealer admin` privi di identita' forte;
  - l'unico `user_candidate_has_insufficient_signals` residuo.
