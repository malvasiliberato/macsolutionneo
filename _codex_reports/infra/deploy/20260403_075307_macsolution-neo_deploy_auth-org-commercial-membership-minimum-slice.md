# File aggiornati

- `config/portal.php`
- `app/Application/Auth/LegacyImport/MySqlAuthOrgLegacyDatasetAdapter.php`
- `app/Application/Auth/LegacyImport/AuthOrgLegacyDryRun.php`
- `tests/Feature/Console/AuthOrgLegacyImportCommandTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# Impatto runtime

Basso e non distruttivo.

Questo slice:

- non introduce migrazioni
- non cambia schema
- non esegue commit
- amplia solo il reconciliation lane per i commerciali usati negli assignment

# Comandi di verifica

## Test

```powershell
php artisan test --filter=AuthOrgLegacyImportCommandTest
```

## Dry-run reale

```powershell
$env:LEGACY_IMPORT_DB_CONNECTION='legacy_mysql'
$env:LEGACY_IMPORT_DB_HOST='localhost'
$env:LEGACY_IMPORT_DB_PORT='3306'
$env:LEGACY_IMPORT_DB_DATABASE='sql1483615_1'
$env:LEGACY_IMPORT_DB_USERNAME='root'
$env:LEGACY_IMPORT_DB_PASSWORD=''
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=legacy-ci3-auth-org --batch=5000
```

# Esito atteso dopo questo slice

- `ready_create_candidates = 4`
- `manual_review_candidates = 17`
- `blocked_candidates = 0`

# Note Laragon / locale

- ambiente di riferimento: `Windows + Laragon`
- il lane legacy reale continua a usare `sql1483615_1`
- nessuna esecuzione `--commit-create-only` in questo step

# Uso pratico nei prossimi task

Il prossimo task puo' eseguire un secondo `create-only` molto piu' stretto:

- solo sui `4` candidati ora sbloccati dal lane commerciale
- senza riaprire il perimetro `dealer admin`
