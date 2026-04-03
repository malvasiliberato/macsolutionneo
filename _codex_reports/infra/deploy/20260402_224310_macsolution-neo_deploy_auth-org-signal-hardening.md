# Auth/Org Signal Hardening - Deploy Notes

## File modificati
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\DefaultAuthOrgLegacyMatchingHeuristics.php`
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\DefaultAuthOrgLegacyMatchingEdgeCases.php`
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\MySqlAuthOrgLegacyDatasetAdapter.php`
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\DefaultAuthOrgLegacyCandidateMatcher.php`
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\AuthOrgLegacyReconciliationReportBuilder.php`
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\AuthOrgLegacyDryRun.php`
- `G:\Mirror\htdocs\macsolutionlaravel\tests\Feature\Console\AuthOrgLegacyImportCommandTest.php`
- `G:\Mirror\htdocs\macsolutionlaravel\AI_CONTEXT.md`
- `G:\Mirror\htdocs\macsolutionlaravel\MASTER_PROGRESS.md`

## Impatto runtime
- nessuna nuova migrazione
- nessuna scrittura distruttiva
- nessun commit mode abilitato
- impatto confinato al layer read-only di reconciliation e al reporting tecnico

## Come rieseguire il prossimo dry-run
In Laragon, con dataset reale configurato:
```powershell
$env:LEGACY_IMPORT_DB_CONNECTION='legacy_mysql'
$env:LEGACY_IMPORT_DB_HOST='localhost'
$env:LEGACY_IMPORT_DB_PORT='3306'
$env:LEGACY_IMPORT_DB_DATABASE='sql1483615_1'
$env:LEGACY_IMPORT_DB_USERNAME='root'
$env:LEGACY_IMPORT_DB_PASSWORD=''
php artisan legacy:import:auth-org --dry-run --dataset=legacy-ci3-auth-org --batch=5000
```

Verifica locale del comportamento hardening con dataset controllato:
```powershell
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=bootstrap-auth-org-signal-hardening --batch=10
```

Suite di verifica:
```powershell
php artisan test
```

## Note Laragon / local setup
- il progetto resta pensato per Windows + Laragon
- il dataset adapter reale puo' usare fallback CLI se la connessione PDO legacy non e' configurata o non e' raggiungibile
- il secondo dry-run reale va rieseguito in sola lettura

## Uso pratico nei prossimi task
- confrontare il secondo dry-run reale con quello precedente
- verificare in particolare:
  - riduzione dei falsi ambigui sui venditori dealer
  - comportamento dei dealer admin come casi speciali
  - breakdown `review_reason_breakdown` e `blocked_reason_breakdown`
- non aprire ancora commit mode finche' i segnali non risultano abbastanza affidabili
