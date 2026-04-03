# File aggiornati

- `app/Application/Auth/LegacyImport/AuthOrgLegacyReconciliationReportBuilder.php`
- `app/Application/Auth/LegacyImport/AuthOrgLegacyCreateOnlyCommitGate.php`
- `app/Application/Auth/LegacyImport/AuthOrgLegacyDryRun.php`
- `tests/Feature/Console/AuthOrgLegacyImportCommandTest.php`
- `MASTER_PROGRESS.md`

# Impatto runtime

Impatto runtime basso e non distruttivo.

Questo slice:

- non introduce nuove migrazioni
- non modifica schema dati
- non esegue nuove scritture nel dominio Neo
- non allarga il gate `create-only`

L'effetto operativo e' sul reconciliation layer:

- il `dry-run` classifica in modo piu' rigoroso i candidati `create-only`
- il gate snapshot espone motivi di esclusione piu' coerenti

# Verifiche eseguite

## Test automatici

Comando:

```powershell
php artisan test --filter=AuthOrgLegacyImportCommandTest
```

Esito:

- `8 passed`

## Dry-run reale rieseguito

Comando usato in ambiente locale Laragon:

```powershell
$env:LEGACY_IMPORT_DB_CONNECTION='legacy_mysql'
$env:LEGACY_IMPORT_DB_HOST='localhost'
$env:LEGACY_IMPORT_DB_PORT='3306'
$env:LEGACY_IMPORT_DB_DATABASE='sql1483615_1'
$env:LEGACY_IMPORT_DB_USERNAME='root'
$env:LEGACY_IMPORT_DB_PASSWORD=''
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=legacy-ci3-auth-org --batch=5000
```

Esito rilevante:

- `ready_create_candidates=0`
- `manual_review_candidates=19`
- `blocked_candidates=0`

# Come usare questo slice nei prossimi task

Da ora in poi il `dry-run` `AUTH/ORG` va letto cosi':

- `ready_create = 0` significa che il gate non ha altri candidati create-only immediatamente trattabili nel dataset reale corrente
- i residui da lavorare sono tutti casi di review tecnica esplicita

Questo consente di aprire un prossimo step piu' mirato, senza rumore da falsi `ready_create`.

# Note Laragon / locale

- ambiente di riferimento: `Windows + Laragon`
- DB Neo locale: `macsolution_neo`
- dataset legacy reale usato nel run: `sql1483615_1`
- il comando resta `read-only`; non usare `--commit-create-only` in questo slice

# Come evitare esecuzioni non prudenziali

- non abilitare commit mode in questo passaggio
- non reinterpretare i `19` review come candidati committabili
- non introdurre merge automatici con target Neo esistenti per chiudere artificialmente i residui
