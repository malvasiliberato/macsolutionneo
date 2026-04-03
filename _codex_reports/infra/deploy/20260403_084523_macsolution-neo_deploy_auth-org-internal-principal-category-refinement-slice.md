# File aggiornati
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\AuthOrgLegacyReconciliationReportBuilder.php`
- `G:\Mirror\htdocs\macsolutionlaravel\tests\Feature\Console\AuthOrgLegacyImportCommandTest.php`
- `G:\Mirror\htdocs\macsolutionlaravel\AI_CONTEXT.md`
- `G:\Mirror\htdocs\macsolutionlaravel\MASTER_PROGRESS.md`

# Impatto runtime
- Nessun impatto su runtime applicativo, migrazioni, seed o persistence.
- Impatto limitato al reporting tecnico del dry-run `AUTH/ORG`.

# Comandi di verifica
```powershell
php artisan test --filter=AuthOrgLegacyImportCommandTest
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=bootstrap-auth-org-create-only --batch=20
```

# Esito operativo
- Suite test verde.
- Il dry-run espone ora `internal_principal_category_breakdown`.

# Come usare questo slice
- Usare `internal_principal_category_breakdown` per leggere subito i residui del lane `internal_platform_principal`.
- Trattare `human_plausible` come area di review/manual reconciliation.
- Trattare `technical_or_test` come area da non promuovere automaticamente.
- Trattare `corporate` come area da target decision separata.

# Note Laragon / locale
- Nessuna nota aggiuntiva.
- Verifiche rieseguibili nel workspace locale Windows + Laragon.
