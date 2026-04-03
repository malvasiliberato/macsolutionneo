# Bootstrap Consolidation Setup

## File e cartelle toccati
- `package.json`
- `package-lock.json`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`
- `_codex_reports/shared/architecture/20260402_184517_macsolution-neo_architecture_bootstrap-consolidation.md`
- `_codex_reports/infra/deploy/20260402_184517_macsolution-neo_deploy_bootstrap-consolidation.md`

## File e cartelle rimossi o ricollocati
- Rimossa la cartella `temp_portal_bootstrap` dalla root del workspace.
- Nessuna ricollocazione di cartelle applicative stabili.

## Note di setup e avvio aggiornate
- Il workspace da usare e' solo la root Laravel/Vue attuale.
- Il naming frontend consolidato e' `macsolution-neo-portal`.
- Le istruzioni operative Laragon restano valide e non richiedono cambi di flusso.

## Come verificare che il workspace consolidato sia ancora avviabile in locale
```powershell
php artisan test
npm.cmd run build
php artisan route:list
```

Verifiche attese:
- test verdi
- build Vite completata
- rotte bootstrap ancora presenti
- progetto apribile in Laragon su `http://macsolutionlaravel.loc/`

## Attenzioni specifiche per Laragon
- Continuare a usare Apache e MySQL di Laragon come ambiente locale standard.
- Continuare a usare `localhost` come `DB_HOST` nell'env locale.
- L'host di riferimento resta `http://macsolutionlaravel.loc/`.
