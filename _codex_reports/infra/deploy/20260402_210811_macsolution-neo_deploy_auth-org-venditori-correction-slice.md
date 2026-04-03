# Auth/Org Venditori Correction Slice - Deploy Notes

## File modificati
- `G:\Mirror\htdocs\macsolutionlaravel\config\portal.php`
- `G:\Mirror\htdocs\macsolutionlaravel\database\seeders\DatabaseSeeder.php`
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\MySqlAuthOrgLegacyDatasetAdapter.php`
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\AuthOrgLegacyDryRun.php`
- `G:\Mirror\htdocs\macsolutionlaravel\app\Application\Auth\LegacyImport\DefaultAuthOrgLegacyCandidateResolver.php`
- `G:\Mirror\htdocs\macsolutionlaravel\tests\Feature\Api\MeTest.php`
- `G:\Mirror\htdocs\macsolutionlaravel\tests\Feature\Console\AuthOrgLegacyImportCommandTest.php`
- `G:\Mirror\htdocs\macsolutionlaravel\AI_CONTEXT.md`
- `G:\Mirror\htdocs\macsolutionlaravel\MASTER_PROGRESS.md`

## Migrazioni / seed toccati
- nessuna nuova migrazione
- seed aggiornato:
  - introdotto account bootstrap `seller@dealer-bootstrap.test`
  - introdotta membership primaria `dealer_seller` sul dealer bootstrap
  - introdotti mapping tecnici bootstrap da `dealer_collaboratore`

## Impatto runtime
- basso e controllato
- nessun modulo business aperto
- nessun redesign del contract `/api/v1/me`
- nessun commit mode abilitato nell'import legacy

## Come verificare localmente
In ambiente Windows + Laragon:

1. riallineare DB locale
```powershell
php artisan migrate:fresh --seed
```

2. verificare il contract API sul foundation
```powershell
php artisan test --filter=MeTest
```

3. verificare il dry-run bootstrap con caso venditore incluso
```powershell
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=bootstrap-auth-org --batch=20
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=dealer_collaboratore --dataset=bootstrap-auth-org --batch=20
```

4. suite completa
```powershell
php artisan test
```

## Note operative per Laragon
- nessuna dipendenza da Docker/Sail/Valet
- la verifica legacy usata nel report architetturale e' stata fatta in sola lettura sul DB locale Laragon
- il dataset adapter reale resta non configurato a runtime applicativo finche' `LEGACY_IMPORT_*` non viene valorizzato

## Nota utile per il prossimo dry-run reale
- dopo questa correzione il filone auth/org puo' riprendere il dry-run reale con una lettura piu' corretta di `dealer_collaboratore`
- il prossimo passo corretto non e' il commit, ma il rafforzamento dei segnali sporchi:
  - email placeholder
  - ruoli legacy poco puliti
  - dealer/admin ancora ambigui
