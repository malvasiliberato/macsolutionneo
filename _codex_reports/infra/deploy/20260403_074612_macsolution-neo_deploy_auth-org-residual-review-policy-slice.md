# File aggiornati

- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# File creati

- `_codex_reports/shared/architecture/20260403_074612_macsolution-neo_architecture_auth-org-residual-review-policy-slice.md`
- `_codex_reports/infra/deploy/20260403_074612_macsolution-neo_deploy_auth-org-residual-review-policy-slice.md`

# Comandi usati

## Raccolta residui reali dal reconciliation layer

Usata una lettura PHP locale del `dry-run` classificato per ottenere:

- breakdown per `candidate_type`
- breakdown per `reason`
- elenco dei `16` `dealer admin`
- dettagli dei `3` casi dipendenti

## Evidenze legacy puntuali

Comandi CLI MySQL usati in ambiente Laragon:

```powershell
mysql -uroot -p --batch --raw --skip-column-names -e "SELECT id,tipo,username,ragione_sociale,is_enable,id_commerciale FROM sql1483615_1.dealer WHERE id IN (2,78,284,292,293,294,295,296,297,298,299,300,301,302,303,306) ORDER BY id"
mysql -uroot -p --batch --raw --skip-column-names -e "SELECT a.id,a.dealer_id,d.username,d.ragione_sociale,a.operatore_id,a.figura,COALESCE(c.username,''),COALESCE(c.nominativo,''),COALESCE(c.email,'') FROM sql1483615_1.dealer_operatore_figura a JOIN sql1483615_1.dealer d ON d.id=a.dealer_id LEFT JOIN sql1483615_1.commerciali c ON c.id=a.operatore_id WHERE a.id IN (1,2) ORDER BY a.id"
mysql -uroot -p --batch --raw --skip-column-names -e "SELECT id,username,nominativo,email,figura FROM sql1483615_1.commerciali WHERE id=741"
```

# Impatto runtime

Nullo.

Questo slice:

- non modifica schema dati
- non introduce nuove migrazioni
- non esegue import o commit
- non cambia il gate `create-only`

# Come usare questo slice nei prossimi task

Da ora il follow-up `AUTH/ORG` deve assumere queste regole:

- `dealer admin` senza identita' forte = review-only
- `mac` non e' una organization implicita
- assignment commerciale senza membership prerequisita = review-only

# Note Laragon / locale

- ambiente di riferimento: `Windows + Laragon`
- legacy dataset usato: `sql1483615_1`
- il client `mysql` locale continua a essere il percorso affidabile per interrogazioni mirate sul legacy quando PDO non dispone delle stesse credenziali
