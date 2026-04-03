# File aggiornati

- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# File creati

- `_codex_reports/shared/architecture/20260403_080013_macsolution-neo_architecture_auth-org-dealer-admin-residual-lane-baseline.md`
- `_codex_reports/infra/deploy/20260403_080013_macsolution-neo_deploy_auth-org-dealer-admin-residual-lane-baseline.md`

# Impatto runtime

Nullo.

Questo task:

- non modifica schema
- non esegue nuovi commit
- non cambia il gate `create-only`

# Verifiche usate

## Evidenza legacy mirata

```powershell
mysql -uroot -p --batch --raw --skip-column-names -e "SELECT id,tipo,username,ragione_sociale,is_enable,id_commerciale,password_change,level FROM sql1483615_1.dealer WHERE tipo='admin' ORDER BY id"
```

## Baseline review reali

Usata una lettura PHP locale del reconciliation layer sul dataset reale `legacy-ci3-auth-org`.

Esito:

- `review_count = 17`
- `dealer_admin_requires_manual_identity_resolution = 16`
- `membership_candidate_requires_resolved_organization_dependency = 1`

# Uso pratico nei prossimi task

Il filone automatico `AUTH/ORG` e' ora fermo in modo ordinato.

Il prossimo task non deve piu':

- riaprire il lane commerciale
- riaprire il gate `create-only`
- cercare altri hardening generici

Se si continua, si continua solo su un lane dedicato ai principal admin legacy.
