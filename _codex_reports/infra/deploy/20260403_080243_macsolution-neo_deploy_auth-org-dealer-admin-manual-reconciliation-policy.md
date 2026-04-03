# File aggiornati

- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# File creati

- `_codex_reports/shared/architecture/20260403_080243_macsolution-neo_architecture_auth-org-dealer-admin-manual-reconciliation-policy.md`
- `_codex_reports/infra/deploy/20260403_080243_macsolution-neo_deploy_auth-org-dealer-admin-manual-reconciliation-policy.md`

# Impatto runtime

Nullo.

Questo slice:

- non modifica schema
- non crea nuovi record
- non esegue commit
- non cambia il gate `create-only`

# Uso pratico della policy

Nei prossimi task il lane `dealer admin` va letto cosi':

- tecnico/test -> non migrare automaticamente
- umano/supporto -> solo link manuale verso account Neo esistente
- corporate ambiguo -> decisione target separata

# Note operative

- il lane commerciale e' gia' chiuso e non va riaperto
- il lane automatico `AUTH/ORG` resta chiuso
- ogni seguito sui `dealer admin` deve essere esplicitamente manuale o semi-manuale
