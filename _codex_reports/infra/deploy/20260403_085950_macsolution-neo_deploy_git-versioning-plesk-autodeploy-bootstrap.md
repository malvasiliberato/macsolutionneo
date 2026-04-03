# File creati o aggiornati
- `G:\Mirror\htdocs\macsolutionlaravel\.gitignore`
- `G:\Mirror\htdocs\macsolutionlaravel\README.md`
- `G:\Mirror\htdocs\macsolutionlaravel\MASTER_PROGRESS.md`
- `G:\Mirror\htdocs\macsolutionlaravel\scripts\deploy\plesk\post-deploy.sh`

# Azioni eseguite
- inizializzato repository locale con:
```powershell
git init
```

# Impatto runtime
- impatto runtime nullo sul portale locale
- nessuna modifica a moduli applicativi, database, API o UI
- introduzione limitata a versionamento e base deploy operativa

# Come usare il bootstrap Git
```powershell
git status
git add .
git commit -m "chore: bootstrap git and plesk deploy base"
```

# Come usare il bootstrap Plesk
Comando consigliato come additional deployment action in Plesk:
```bash
bash scripts/deploy/plesk/post-deploy.sh
```

Variabili opzionali:
```bash
PHP_BIN=/opt/plesk/php/8.2/bin/php
COMPOSER_BIN=/usr/bin/composer
RUN_MIGRATIONS=1
RUN_FRONTEND_BUILD=0
```

# Prerequisiti minimi Plesk
- repository Git collegato al path corretto
- `.env` gia' presente sul server
- cartelle `storage` e `bootstrap/cache` scrivibili
- PHP CLI e Composer disponibili
- Node/npm solo se si vuole buildare sul server

# Nota pratica
- Questo step prepara l'auto deploy, ma non forza ancora un deploy reale.
- Il prossimo passo prudente e' configurare remote/branch di riferimento e verificare l'esecuzione del post-deploy in un ambiente Plesk controllato.
