# File creati o aggiornati
- `app/Application/Auth/LegacyImport/Contracts/AuthOrgLegacyMatchingConfidence.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# Namespace introdotti
- `App\Application\Auth\LegacyImport\Contracts`

# Impatto runtime
- Nullo.
- Nessuna route, migration, seeder, command signature o configurazione applicativa modificata.

# Come usare questa base nei prossimi task
- Usare il contratto `AuthOrgLegacyMatchingConfidence` come riferimento per il primo slice implementativo del confidence scoring.
- Mantenere la confidence come layer read-only del matching, non come sostituto di `match_status`.
- Agganciare il prossimo slice a `legacy:import:auth-org --dry-run`, senza aprire ancora commit mode o import completi.

# Verifica minima
- Controllare l'esistenza del file contratto nel namespace auth/org legacy import.
- Verificare che `AI_CONTEXT.md` e `MASTER_PROGRESS.md` riportino `MSN-AUTH-019` come `validato` e `MSN-AUTH-020` come `ready`.

# Attenzione operativa
- Ambiente di riferimento: Windows + Laragon.
- Nessuna azione aggiuntiva richiesta per avvio locale o database.
