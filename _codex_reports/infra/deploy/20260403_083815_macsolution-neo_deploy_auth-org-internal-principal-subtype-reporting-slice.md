# File aggiornati
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\DefaultAuthOrgLegacyCandidateResolver.php`
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\AuthOrgLegacyReconciliationReportBuilder.php`
- `G:\Mirror\htdocs\macsolutionlaravel\tests\Feature\Console\AuthOrgLegacyImportCommandTest.php`
- `G:\Mirror\htdocs\macsolutionlaravel\AI_CONTEXT.md`
- `G:\Mirror\htdocs\macsolutionlaravel\MASTER_PROGRESS.md`

# Impatto runtime
- Nessun impatto sul runtime applicativo del portale.
- Nessuna migrazione, seed o modifica al dominio.
- Impatto limitato al dry-run AUTH/ORG e alla reportistica tecnica di reconciliation.

# Comandi di verifica
```powershell
php artisan test --filter=AuthOrgLegacyImportCommandTest
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=bootstrap-auth-org-create-only --batch=20
```

# Esito operativo
- Suite test verde.
- Il reconciliation snapshot espone ora `lane_subtype_breakdown`.
- I principal interni non ancora ben classificati vengono mostrati come `unclassified_internal_principal`.

# Come usare questo slice
- Usare `lane_subtype_breakdown` come base per leggere meglio i residui admin/backoffice.
- Evitare di usare il solo lane aggregato quando il task riguarda principal interni.
- Trattare `unclassified_internal_principal` come segnale di audit/decisione target ancora aperta, non come scorciatoia per ampliare il gate.

# Note Laragon / locale
- Nessuna nota aggiuntiva.
- Verifiche rieseguibili nel workspace locale Windows + Laragon.
