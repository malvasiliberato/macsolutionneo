# Auth/Org Import Dry-Run Second Pass - Deploy Notes

## File toccati
- `G:\Mirror\htdocs\macsolutionlaravel\MASTER_PROGRESS.md`

## Comandi usati
```powershell
$env:LEGACY_IMPORT_DB_CONNECTION='legacy_mysql'
$env:LEGACY_IMPORT_DB_HOST='localhost'
$env:LEGACY_IMPORT_DB_PORT='3306'
$env:LEGACY_IMPORT_DB_DATABASE='sql1483615_1'
$env:LEGACY_IMPORT_DB_USERNAME='root'
$env:LEGACY_IMPORT_DB_PASSWORD=''
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=legacy-ci3-auth-org --batch=5000
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=dealer_collaboratore --dataset=legacy-ci3-auth-org --batch=5000
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=dealer --dataset=legacy-ci3-auth-org --batch=5000
```

Query di supporto in sola lettura usate per chiarire i residui legacy:
- count placeholder/non-email in `dealer_collaboratore`
- count duplicate email groups in `dealer_collaboratore`
- count duplicate username in `dealer`
- count `dealer admin`

## Prerequisiti di esecuzione
- ambiente Windows + Laragon
- database legacy locale disponibile come `sql1483615_1`
- accesso MySQL locale `root`
- config `LEGACY_IMPORT_*` valorizzata nella shell corrente oppure fallback CLI disponibile

## Impatto runtime
- nessuna migrazione
- nessuna scrittura nel dominio Neo
- nessuna scrittura nei mapping tecnici
- impatto runtime nullo o trascurabile, task interamente read-only

## Come rieseguire il dry-run in locale
```powershell
$env:LEGACY_IMPORT_DB_CONNECTION='legacy_mysql'
$env:LEGACY_IMPORT_DB_HOST='localhost'
$env:LEGACY_IMPORT_DB_PORT='3306'
$env:LEGACY_IMPORT_DB_DATABASE='sql1483615_1'
$env:LEGACY_IMPORT_DB_USERNAME='root'
$env:LEGACY_IMPORT_DB_PASSWORD=''
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=legacy-ci3-auth-org --batch=5000
```

## Note Laragon / local setup
- il progetto resta Laragon-first
- il dataset adapter reale puo' ancora usare fallback CLI se la connessione PDO legacy non e' risolta correttamente
- questo task non abilita commit mode

## Uso pratico del risultato
- il filone `AUTH/ORG` puo' ora passare dal `NO-GO` a un `GO condizionato`
- il prossimo task corretto e' il design del commit gate controllato, non un import massivo diretto
