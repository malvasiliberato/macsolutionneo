# File aggiornati

- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# File creati

- `_codex_reports/shared/architecture/20260403_080520_macsolution-neo_architecture_auth-org-case-mac-target-decision.md`
- `_codex_reports/infra/deploy/20260403_080520_macsolution-neo_deploy_auth-org-case-mac-target-decision.md`

# Impatto runtime

Nullo.

Questo slice:

- non modifica schema
- non crea record
- non cambia il gate `create-only`

# Evidenze usate

## Query legacy mirate

```powershell
mysql -uroot -p --batch --raw --skip-column-names -e "SELECT id,tipo,username,ragione_sociale,is_enable,password_change,level,id_commerciale FROM sql1483615_1.dealer WHERE username='mac'"
mysql -uroot -p --batch --raw --skip-column-names -e "SELECT c.id,c.id_dealer,c.username,c.nominativo,c.email,c.ruolo,d.username AS dealer_code,d.ragione_sociale FROM sql1483615_1.dealer_collaboratore c JOIN sql1483615_1.dealer d ON d.id=c.id_dealer WHERE d.username='mac'"
```

## Verifica Neo locale

Usata lettura locale del DB Neo per confermare:

- assenza di `organization.code = mac`
- assenza di membership su `mac`

# Uso pratico nei prossimi task

Il caso `mac` e' ora considerato deciso a livello target:

- non va sbloccato automaticamente
- non va riaperto come problema di matching
- non va usato per riaprire il gate `create-only`
