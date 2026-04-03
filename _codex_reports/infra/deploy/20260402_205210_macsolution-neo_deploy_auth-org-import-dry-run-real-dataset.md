# File toccati
- `config/legacy_import.php`
- `app/Application/Auth/LegacyImport/MySqlAuthOrgLegacyDatasetAdapter.php`
- `app/Application/Auth/LegacyImport/DefaultAuthOrgLegacyCandidateMatcher.php`
- `app/Application/Auth/LegacyImport/AuthOrgLegacyReconciliationReportBuilder.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# Comandi usati
```powershell
php artisan test

$env:LEGACY_IMPORT_DB_CONNECTION='legacy_mysql'
$env:LEGACY_IMPORT_DB_HOST='localhost'
$env:LEGACY_IMPORT_DB_PORT='3306'
$env:LEGACY_IMPORT_DB_DATABASE='sql1483615_1'
$env:LEGACY_IMPORT_DB_USERNAME='root'
$env:LEGACY_IMPORT_DB_PASSWORD=''
php artisan legacy:import:auth-org --dry-run --dataset=legacy-ci3-auth-org --batch=5000
```

# Prerequisiti di esecuzione
- ambiente locale Windows + Laragon
- client `mysql` disponibile nel PATH Laragon
- dataset legacy reale presente localmente

# Impatto runtime
- nessun commit mode abilitato
- nessuna scrittura distruttiva sul dominio Neo
- solo miglioramento del dry-run read-only e del reporting

# Come rieseguire il dry-run in locale
- impostare le variabili `LEGACY_IMPORT_*` come sopra
- eseguire il comando Artisan di dry-run
- verificare nell'output:
  - summary batch
  - reconciliation snapshot
  - `commit_mode=not_available`
  - `batch_result=needs_reconciliation`

# Note Laragon/local setup
- in questo ambiente la connessione PDO legacy non era sufficiente ad aprire il dataset reale
- e' stato quindi introdotto un fallback CLI read-only dentro il dataset adapter, coerente con Laragon locale e senza impatto sul dominio Neo
