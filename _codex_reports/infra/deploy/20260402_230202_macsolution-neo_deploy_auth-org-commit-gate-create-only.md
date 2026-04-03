# Auth/Org Commit Gate Create-Only - Deploy Notes

## File creati / modificati
- `G:\Mirror\htdocs\macsolutionlaravel\config\legacy_import.php`
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\AuthOrgLegacyCreateOnlyCommitGate.php`
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\AuthOrgLegacyDryRun.php`
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\AuthOrgLegacyReconciliationReportBuilder.php`
- `G:\Mirror\htdocs\macsolutionlaravel\app\Console\Commands\LegacyImport\AuthOrgLegacyImportCommand.php`
- `G:\Mirror\htdocs\macsolutionlaravel\tests\Feature\Console\AuthOrgLegacyImportCommandTest.php`
- `G:\Mirror\htdocs\macsolutionlaravel\MASTER_PROGRESS.md`

## Comandi introdotti / aggiornati
Dry-run invariato:
```powershell
php artisan legacy:import:auth-org --dry-run
```

Nuova modalita' create-only protetta:
```powershell
php artisan legacy:import:auth-org --commit-create-only --confirm-create-only
```

## Impatto runtime
- nessuna nuova migrazione
- nessuna modifica ai moduli business
- impatto confinato al layer import/reconciliation auth/org
- scrittura ammessa solo quando si usa esplicitamente il gate create-only

## Come verificare localmente il gate create-only
Verifica completa:
```powershell
php artisan migrate:fresh --seed
php artisan test
```

Prova tecnica controllata del gate:
```powershell
php artisan legacy:import:auth-org --source-system=legacy_ci3 --legacy-table=all --dataset=bootstrap-auth-org-create-only --batch=20 --commit-create-only --confirm-create-only
```

## Come evitare esecuzioni non prudenziali
- non usare il gate senza avere prima un dry-run valido
- il comando richiede conferma esplicita `--confirm-create-only`
- il gate non committa review, blocked o merge esistenti
- il gate reale su dataset CI3 non va lanciato senza checklist operativa dedicata

## Note Laragon / local setup
- il progetto resta pensato per Windows + Laragon
- il gate e' stato verificato su dataset controllato interno, non con import massivo reale
- il dominio synthetic email di default e':
  - `legacy-auth.macsolution-neo.local`
