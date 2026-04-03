# File aggiornati
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\AuthOrgLegacyReconciliationReportBuilder.php`
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\AuthOrgLegacyDryRun.php`
- `G:\Mirror\htdocs\macsolutionlaravel\tests\Feature\Console\AuthOrgLegacyImportCommandTest.php`
- `G:\Mirror\htdocs\macsolutionlaravel\AI_CONTEXT.md`
- `G:\Mirror\htdocs\macsolutionlaravel\MASTER_PROGRESS.md`

# Impatto runtime
- Nessun impatto su runtime applicativo, migrazioni, seed o persistence.
- Impatto limitato al dry-run `AUTH/ORG` e alla reportistica tecnica di reconciliation.

# Comandi di verifica
```powershell
php artisan test --filter=AuthOrgLegacyImportCommandTest
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=bootstrap-auth-org-internal-principal-review --batch=20
```

# Esito operativo
- Suite test verde.
- Il dry-run espone ora `internal_principal_review_action_breakdown`.

# Come usare questo slice
- Usare `manual_link_candidate` per i casi umani plausibili da confrontare con account Neo reali.
- Usare `manual_review_only` per i casi umani che restano review ma senza segnale sufficiente per link candidato.
- Usare `do_not_migrate_automatically` per i principal tecnici/test.
- Usare `manual_target_decision_required` per i casi corporate ambigui.

# Note Laragon / locale
- Nessuna nota aggiuntiva.
- Verifiche rieseguibili nel workspace locale Windows + Laragon.
