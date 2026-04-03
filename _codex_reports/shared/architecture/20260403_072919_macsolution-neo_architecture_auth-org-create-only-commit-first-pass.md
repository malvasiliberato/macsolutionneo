# Auth/Org Create-Only Commit First Pass

## Contesto
Il filone `AUTH/ORG` arrivava a questo passaggio con:
- secondo dry-run reale su `sql1483615_1` classificato `GO condizionato`;
- `ready_create_candidates = 1604`
- `manual_review_candidates = 17`
- `blocked_candidates = 0`
- gate tecnico `create-only` gia' implementato e validato su dataset controllato.

## Obiettivo del task
Eseguire il primo commit controllato reale `AUTH/ORG` in locale, usando il gate `create-only`, senza toccare i casi `needs_review` e senza introdurre merge aggressivi.

## Modalita' di esecuzione
Base locale riallineata prima del run:
```powershell
php artisan migrate:fresh --seed
```

Commit controllato reale:
```powershell
$env:LEGACY_IMPORT_DB_CONNECTION='legacy_mysql'
$env:LEGACY_IMPORT_DB_HOST='localhost'
$env:LEGACY_IMPORT_DB_PORT='3306'
$env:LEGACY_IMPORT_DB_DATABASE='sql1483615_1'
$env:LEGACY_IMPORT_DB_USERNAME='root'
$env:LEGACY_IMPORT_DB_PASSWORD=''
php artisan legacy:import:auth-org --source-system=legacy_ci3 --legacy-table=all --dataset=legacy-ci3-auth-org --batch=5000 --commit-create-only --confirm-create-only
```

Rerun immediato per verificare la stabilita' del gate:
- stesso comando
- nessuna nuova creazione attesa

## Esito del primo commit create-only reale
Summary del primo run:
- `records_scanned = 1621`
- `ready_create_candidates = 1605`
- `manual_review_candidates = 16`
- `blocked_candidates = 0`
- `committed_users = 654`
- `committed_organizations = 272`
- `committed_memberships = 425`
- `committed_assignments = 0`
- `mappings_created = 1351`
- `synthetic_emails_assigned = 505`

Snapshot del gate:
- review escluse: `16`
- review reason:
  - `dealer_admin_requires_manual_identity_resolution = 16`
- runtime exclusions:
  - `organization_unique_key_conflict = 1`
  - `user_unique_key_conflict = 11`
  - `membership_missing_user_or_organization_dependency = 1`
  - `assignment_missing_membership_or_dealer_dependency = 2`

## Delta applicativo osservato nel DB Neo
Stato seed iniziale:
- `users = 2`
- `organizations = 2`
- `memberships = 2`
- `assignments = 1`
- `mappings = 7`

Stato dopo il commit:
- `users = 656`
- `organizations = 274`
- `memberships = 427`
- `assignments = 1`
- `mappings = 1358`
- `create_only_mappings = 1351`
- `synthetic_email_users = 505`

Interpretazione:
- il primo passaggio ha popolato in modo consistente `users`, `organizations` e `organization_memberships`;
- il perimetro `assignment` non e' ancora maturato davvero nel dataset reale di questo passaggio;
- l'uso di synthetic email tecnica e' stato necessario in modo sostanziale per i venditori dealer-scoped con identity email non affidabile.

## Comportamento al rerun
Rerun immediato del gate sullo stesso dataset reale:
- `ready_create_candidates = 3`
- `manual_review_candidates = 16`
- `committed_users = 0`
- `committed_organizations = 0`
- `committed_memberships = 0`
- `committed_assignments = 0`

Residui runtime al rerun:
- `membership_missing_user_or_organization_dependency = 1`
- `assignment_missing_membership_or_dealer_dependency = 2`

Lettura:
- il gate e' sostanzialmente stabile e non duplica;
- restano alcuni residui create-only che dipendono da dipendenze non risolte nel primo passaggio, non da un difetto di idempotenza globale.

## Casi esclusi confermati
Restano fuori:
- `16` dealer admin senza identita' forte
- qualunque review residua
- qualunque merge con target Neo esistenti
- i conflitti unici intercettati dal gate

## Rischi residui
- i `dealer admin` restano il principale punto aperto;
- `505` synthetic email indicano che il legacy resta povero di identita' email affidabili sui dealer seller;
- i conflitti unici e le dipendenze mancanti vanno trattati in un follow-up prima di un secondo passaggio piu' ampio.

## Valutazione
Il primo commit controllato `create-only` e' riuscito.

Valutazione complessiva:
- `GO` al create-only controllato locale: raggiunto
- `NO-GO` ancora implicito per qualunque commit piu' ampio o merge-based

## Raccomandazione finale
Aprire un follow-up `AUTH/ORG reconciliation post-create-only` focalizzato su:
- `dealer admin` esclusi
- conflitti di chiave univoca
- dipendenze mancanti su membership/assignment
- criteri per un eventuale secondo passaggio create-only o per aprire il review flow
