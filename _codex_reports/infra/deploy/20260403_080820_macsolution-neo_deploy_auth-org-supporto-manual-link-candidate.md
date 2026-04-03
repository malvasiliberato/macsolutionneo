# File aggiornati

- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# File creati

- `_codex_reports/shared/architecture/20260403_080820_macsolution-neo_architecture_auth-org-supporto-manual-link-candidate.md`
- `_codex_reports/infra/deploy/20260403_080820_macsolution-neo_deploy_auth-org-supporto-manual-link-candidate.md`

# Impatto runtime

Nullo.

Questo slice:

- non modifica schema
- non crea record
- non esegue commit
- non cambia il gate `create-only`

# Evidenze usate

## Query legacy

```powershell
mysql -uroot -p --batch --raw --skip-column-names -e "SELECT id,tipo,username,ragione_sociale,is_enable,password_change,level FROM sql1483615_1.dealer WHERE username='supporto'"
mysql -uroot -p --batch --raw --skip-column-names -e "SELECT c.id,c.id_dealer,c.username,c.nominativo,c.email,c.ruolo,d.username AS dealer_code,d.ragione_sociale FROM sql1483615_1.dealer_collaboratore c JOIN sql1483615_1.dealer d ON d.id=c.id_dealer WHERE d.username='supporto'"
```

## Verifica Neo

Usata lettura locale del DB Neo per verificare la presenza dell'utente:

- `liberato.malvasi@hotmail.com`

# Uso pratico nei prossimi task

`supporto` puo' ora essere trattato come:

- `manual_link_candidate`

ma non come:

- auto-link candidate
- create-only candidate
