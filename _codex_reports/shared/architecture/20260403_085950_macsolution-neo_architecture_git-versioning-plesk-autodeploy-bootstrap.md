# Contesto
Prima di proseguire con altri macro-step `AUTH/ORG`, il workspace Mac Solution Neo aveva bisogno di una base DevOps minima ma reale:
- repository Git inizializzato;
- regole di versionamento coerenti col bootstrap Laravel + Vue;
- preparazione prudente all'auto deploy su Plesk.

# Obiettivo del task
Configurare il workspace per:
- versionamento Git locale;
- esclusione corretta di artefatti sensibili o runtime;
- auto deploy prudente su Plesk senza introdurre CI/CD pesante o feature di dominio.

# Scelte adottate
## Git
- inizializzato il repository Git locale con `git init`
- mantenuto il focus su una base repository pulita, senza introdurre branching o remote forzati in questo step

## .gitignore
Rafforzato per un progetto Laravel + Vue reale:
- esclusi `.env` e varianti locali sensibili
- esclusi `vendor`, `node_modules`, build frontend e artefatti runtime
- esclusi cache, log e directory `storage/framework/*`
- mantenuti tracciabili `.env.example` e `.env.testing`

## Plesk auto deploy
Introdotto uno script prudente:
- `scripts/deploy/plesk/post-deploy.sh`

Lo script:
- esegue `composer install --no-dev`
- pulisce e ricostruisce le cache Laravel
- crea `public/storage` se manca
- esegue migrazioni solo se abilitate esplicitamente
- esegue build frontend solo se abilitata esplicitamente e se `npm` e' disponibile
- riavvia le queue in modo non bloccante

# Motivazione delle scelte
- `Plesk` richiede una base semplice, governabile e compatibile con deploy via pull Git + post-deploy action.
- Non e' prudente assumere:
  - Node disponibile sul server
  - migrazioni automatiche sempre desiderabili
  - permessi o filesystem identici al locale Laragon
- Per questo lo script e' conservativo e guidato da variabili esplicite.

# Cosa e' stato implementato davvero
- repository Git locale inizializzato
- `.gitignore` riallineato al workspace reale
- script `post-deploy` per Plesk creato
- `README.md` aggiornato con istruzioni Git + Plesk
- `MASTER_PROGRESS.md` aggiornato in modo minimo per rendere leggibile il filone trasversale di versionamento/deploy

# Cosa e' volutamente rimandato
- configurazione di remote Git
- branching strategy definitiva
- pipeline CI/CD completa
- deploy production-specific con credenziali o hook sensibili
- migrazioni automatiche sempre-on
- build frontend server-side obbligatoria

# Rischi residui
- Su Plesk la disponibilita' reale di `php`, `composer`, `npm` e permessi filesystem va verificata sul server.
- Se il server non ha Node, la build frontend dovra' essere prodotta altrove o gestita con una strategia dedicata.
- Le migrazioni restano volutamente disabilitate di default per evitare deploy aggressivi.

# Perche' questa base e' coerente col progetto
- rispetta `minimum migration risk / maximum architectural clarity`
- non introduce overengineering
- prepara il workspace a essere versionato e distribuito senza sporcare il dominio
- resta compatibile con il contesto locale Windows + Laragon e con un target deploy Plesk
