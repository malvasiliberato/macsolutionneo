# File aggiornati

- `app/Application/Auth/LegacyImport/AuthOrgLegacyCreateOnlyCommitGate.php`
- `tests/Feature/Console/AuthOrgLegacyImportCommandTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# File creati

- `_codex_reports/shared/architecture/20260403_075833_macsolution-neo_architecture_auth-org-create-only-second-pass-commercial-lane.md`
- `_codex_reports/infra/deploy/20260403_075833_macsolution-neo_deploy_auth-org-create-only-second-pass-commercial-lane.md`

# Impatto runtime

Basso ma reale sul dominio locale:

- eseguito un commit `create-only` sul dataset legacy reale
- create solo `2` membership + `2` assignment del lane commerciale
- nessuna scrittura su `users` o `organizations`

# Verifiche eseguite

## Test

```powershell
php artisan test --filter=AuthOrgLegacyImportCommandTest
```

Esito:

- `8 passed`

## Commit reale

```powershell
$env:LEGACY_IMPORT_DB_CONNECTION='legacy_mysql'
$env:LEGACY_IMPORT_DB_HOST='localhost'
$env:LEGACY_IMPORT_DB_PORT='3306'
$env:LEGACY_IMPORT_DB_DATABASE='sql1483615_1'
$env:LEGACY_IMPORT_DB_USERNAME='root'
$env:LEGACY_IMPORT_DB_PASSWORD=''
php artisan legacy:import:auth-org --source-system=legacy_ci3 --legacy-table=all --dataset=legacy-ci3-auth-org --batch=5000 --commit-create-only --confirm-create-only
```

## Rerun di conferma

Stesso comando rieseguito:

- `ready_create_candidates = 0`
- nessuna nuova scrittura

# Note Laragon / locale

- ambiente di riferimento: `Windows + Laragon`
- dataset legacy reale: `sql1483615_1`
- il lane `dealer admin` resta fuori da qualunque commit automatico

# Uso pratico nei prossimi task

Da ora il follow-up `AUTH/ORG` non deve piu' riaprire il lane commerciale gia' chiuso.

Il prossimo task deve concentrarsi solo sui `17` review residue:

- `16` principal `dealer admin`
- `1` caso `mac`
