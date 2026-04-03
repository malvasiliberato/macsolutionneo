# File aggiornati

- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# File creati

- `_codex_reports/shared/architecture/20260403_074903_macsolution-neo_architecture_auth-org-dealer-admin-commercial-membership-target-decision.md`
- `_codex_reports/infra/deploy/20260403_074903_macsolution-neo_deploy_auth-org-dealer-admin-commercial-membership-target-decision.md`

# Impatto runtime

Nullo.

Questo slice:

- non cambia schema
- non introduce migrazioni
- non scrive nuovi record
- non modifica il gate `create-only`

# Evidenze operative usate

- verifica del modello Neo attuale:
  - `DealerOperatorAssignment` dipende da `operator_membership_id`
  - `OrganizationMembership` resta quindi prerequisito reale per ogni assignment
- verifica legacy mirata via CLI MySQL:
  - `dealer.tipo=admin` per i 16 principal review
  - `dealer_operatore_figura` ids `1` e `2`
  - `commerciali.id = 741`

# Come usare questa decisione nei prossimi task

- non riaprire il gate `create-only` per i `dealer admin`
- non creare assignment senza membership
- se si vuole sbloccare i `2` assignment commerciali, aprire un slice dedicato alla membership minima dei commerciali

# Note Laragon / locale

- ambiente di riferimento: `Windows + Laragon`
- per query legacy mirate il client `mysql` locale resta il percorso piu' affidabile in questo ambiente
