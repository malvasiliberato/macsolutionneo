# File aggiornati

- `MASTER_PROGRESS.md`

# File creati

- `_codex_reports/shared/architecture/20260403_074440_macsolution-neo_architecture_auth-org-residual-review-baseline.md`
- `_codex_reports/infra/deploy/20260403_074440_macsolution-neo_deploy_auth-org-residual-review-baseline.md`

# Comandi usati

## Dry-run reale di baseline

```powershell
$env:LEGACY_IMPORT_DB_CONNECTION='legacy_mysql'
$env:LEGACY_IMPORT_DB_HOST='localhost'
$env:LEGACY_IMPORT_DB_PORT='3306'
$env:LEGACY_IMPORT_DB_DATABASE='sql1483615_1'
$env:LEGACY_IMPORT_DB_USERNAME='root'
$env:LEGACY_IMPORT_DB_PASSWORD=''
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=legacy-ci3-auth-org --batch=5000
```

## Raccolta residui strutturata

Usata una lettura PHP locale del reconciliation layer per estrarre:

- breakdown per `candidate_type`
- breakdown per `reconciliation_override_reason`
- elenco dei `16` dealer admin review
- dettagli dei `3` casi dipendenti

# Impatto runtime

Nullo.

Questo task:

- non ha modificato schema dati
- non ha introdotto nuove migrazioni
- non ha eseguito commit `create-only`
- non ha scritto nuovi record nel dominio Neo

# Come rieseguire la baseline in locale

1. assicurarsi che Laragon abbia MySQL attivo
2. impostare le variabili `LEGACY_IMPORT_*`
3. eseguire il `dry-run` read-only mostrato sopra

Per una verifica rapida dei numeri attesi dopo questo slice:

- `ready_create_candidates=0`
- `manual_review_candidates=19`
- `blocked_candidates=0`

# Note Laragon / local setup

- ambiente di riferimento: `Windows + Laragon`
- database Neo locale: `macsolution_neo`
- dataset legacy reale usato: `sql1483615_1`
- il run resta volutamente read-only

# Uso pratico nei prossimi task

Da ora il follow-up `AUTH/ORG` non deve piu' partire da contatori generici, ma da questi `19` casi residuali gia' consolidati.

Il prossimo task puo' concentrarsi soltanto su:

- `dealer admin`
- caso `mac`
- `2` assignment commerciali senza membership prerequisita
