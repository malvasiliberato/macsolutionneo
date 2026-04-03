# File aggiornati

- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# File creati

- `_codex_reports/shared/architecture/20260403_080133_macsolution-neo_architecture_auth-org-dealer-admin-target-categories.md`
- `_codex_reports/infra/deploy/20260403_080133_macsolution-neo_deploy_auth-org-dealer-admin-target-categories.md`

# Impatto runtime

Nullo.

Questo slice:

- non cambia schema
- non esegue commit
- non riapre il gate `create-only`

# Evidenze operative usate

## Query legacy mirata

```powershell
mysql -uroot -p --batch --raw --skip-column-names -e "SELECT id,tipo,username,ragione_sociale,is_enable,id_commerciale,password_change,level FROM sql1483615_1.dealer WHERE tipo='admin' ORDER BY id"
```

## Baseline review residue

Usata una lettura locale del `dry-run` classificato su `legacy-ci3-auth-org` con variabili `LEGACY_IMPORT_*` impostate.

Esito:

- `review_count = 17`
- `dealer_admin_requires_manual_identity_resolution = 16`
- `membership_candidate_requires_resolved_organization_dependency = 1`

# Uso pratico nei prossimi task

Da ora i `dealer admin` legacy vanno letti in tre categorie:

- tecnici/test
- umani/supporto
- corporate ambigui

Questa distinzione serve solo a guidare la review successiva; non autorizza alcun import automatico aggiuntivo.
