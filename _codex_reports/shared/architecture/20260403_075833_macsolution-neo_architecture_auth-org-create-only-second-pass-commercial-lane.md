# Contesto

Nel filone `AUTH/ORG` il lane commerciale era stato appena sbloccato dal slice:

- `dealer_operatore_figura` -> membership minima `dealer_operator_member`
- assignment validi solo sopra membership esplicita

Il dry-run reale aveva quindi mostrato:

- `ready_create_candidates = 4`
- `manual_review_candidates = 17`

con i `17` review ancora concentrati su:

- `16` `dealer admin`
- `1` caso `mac`

# Obiettivo del task

Eseguire il secondo passaggio `create-only` AUTH/ORG limitato di fatto al lane commerciale appena sbloccato, senza toccare:

- i `dealer admin` legacy
- il caso `mac`
- merge o match aggressivi su target Neo esistenti

# Esecuzione reale

Comando eseguito sul dataset reale `sql1483615_1`:

```powershell
$env:LEGACY_IMPORT_DB_CONNECTION='legacy_mysql'
$env:LEGACY_IMPORT_DB_HOST='localhost'
$env:LEGACY_IMPORT_DB_PORT='3306'
$env:LEGACY_IMPORT_DB_DATABASE='sql1483615_1'
$env:LEGACY_IMPORT_DB_USERNAME='root'
$env:LEGACY_IMPORT_DB_PASSWORD=''
php artisan legacy:import:auth-org --source-system=legacy_ci3 --legacy-table=all --dataset=legacy-ci3-auth-org --batch=5000 --commit-create-only --confirm-create-only
```

# Esito del secondo passaggio

## Summary di commit

- `records_scanned = 1623`
- `ready_create_candidates = 4`
- `manual_review_candidates = 17`
- `blocked_candidates = 0`
- `committed_users = 0`
- `committed_organizations = 0`
- `committed_memberships = 2`
- `committed_assignments = 2`
- `mappings_created = 4`
- `excluded_due_missing_dependencies = 0`
- `gate_result = create_only_commit_ok`

## Cosa e' stato creato

Solo lane commerciale:

- `2` `organization_memberships` con `role_code = dealer_operator_member`
- `2` `dealer_operator_assignments` collegati

Nessun impatto su:

- `dealer admin`
- `case mac`
- principal o organization fuori perimetro

# Verifica di idempotenza

Il rerun successivo dello stesso comando restituisce:

- `ready_create_candidates = 0`
- `manual_review_candidates = 17`
- `blocked_candidates = 0`
- nessuna nuova scrittura

Quindi il lane commerciale e' stato chiuso in modo pulito e il gate resta idempotente.

# Stato locale osservato dopo il passaggio

Nel DB Neo locale risultano:

- `users = 656`
- `organizations = 275`
- `memberships = 429`
- `assignments = 3`
- `mappings = 1363`
- `create_only_mappings = 1356`

# Casi esplicitamente esclusi

Restano fuori:

- `16` `dealer_admin_requires_manual_identity_resolution`
- `1` `membership_candidate_requires_resolved_organization_dependency`

Questi casi non sono stati toccati da questo passaggio.

# Cosa e' stato implementato davvero

- secondo passaggio `create-only` eseguito sul dataset reale
- verifica di rerun senza nuove scritture
- aggiornamento contesto/plancia del filone

# Cosa e' stato volutamente rimandato

- nessun lane dedicato ai `dealer admin`
- nessuna promozione implicita del principal `mac`
- nessuna UI di review
- nessun commit al di fuori del lane commerciale

# Raccomandazione operativa finale

Il filone `AUTH/ORG` puo' ora concentrarsi solo sui `17` review residue, con un prossimo step dedicato a:

- principal amministrativi legacy
- decisione esplicita sul caso `mac`
