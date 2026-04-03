# Auth/Org Create-Only Commit First Pass - Deploy Notes

## File toccati
- `G:\Mirror\htdocs\macsolutionlaravel\MASTER_PROGRESS.md`

## Comandi usati
```powershell
php artisan migrate:fresh --seed
```

```powershell
$env:LEGACY_IMPORT_DB_CONNECTION='legacy_mysql'
$env:LEGACY_IMPORT_DB_HOST='localhost'
$env:LEGACY_IMPORT_DB_PORT='3306'
$env:LEGACY_IMPORT_DB_DATABASE='sql1483615_1'
$env:LEGACY_IMPORT_DB_USERNAME='root'
$env:LEGACY_IMPORT_DB_PASSWORD=''
php artisan legacy:import:auth-org --source-system=legacy_ci3 --legacy-table=all --dataset=legacy-ci3-auth-org --batch=5000 --commit-create-only --confirm-create-only
```

Rerun di verifica:
- stesso comando

## Prerequisiti di esecuzione
- Windows + Laragon
- DB legacy locale disponibile come `sql1483615_1`
- DB Neo locale disponibile e riallineabile con `migrate:fresh --seed`
- gate `create-only` gia' presente nel workspace

## Impatto runtime
- scrittura locale reale su DB Neo
- nessuna scrittura sul DB legacy
- nessuna modifica ai moduli business
- nessun merge con target Neo esistenti

## Come verificare localmente
1. riallineare la base locale
2. eseguire il gate `create-only`
3. rileggere conteggi e mapping
4. fare un rerun per verificare che non duplichi

## Come evitare esecuzioni non prudenziali
- usare solo `--commit-create-only --confirm-create-only`
- non lanciare il commit senza avere gia' dry-run e gate validati
- non usare questo passaggio per includere i `dealer admin` o altri `needs_review`

## Note Laragon / local setup
- il passaggio e' stato eseguito in locale
- il timeout del primo tentativo shell era troppo basso per il dataset reale; il run valido si e' chiuso correttamente con timeout esteso
- i `505` synthetic email creati sono attesi nel perimetro create-only e non vanno interpretati come cleanup definitivo del legacy
