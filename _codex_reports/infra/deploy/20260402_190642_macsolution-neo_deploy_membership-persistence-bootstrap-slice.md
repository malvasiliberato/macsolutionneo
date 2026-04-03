# Mac Solution Neo - Deploy Notes Membership Persistence Bootstrap Slice

## File toccati
- `app/Models/Organization.php`
- `app/Models/OrganizationMembership.php`
- `app/Models/User.php`
- `app/Support/Auth/ResolveAuthenticatedPortalContext.php`
- `database/migrations/2026_04_02_190300_create_organizations_tables.php`
- `database/seeders/DatabaseSeeder.php`
- `tests/Feature/Api/MeTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Impatto setup locale
- Nuova migrazione richiesta.
- Seeder aggiornato.
- Nessuna nuova dipendenza frontend o backend.
- Nessuna modifica richiesta a host Laragon o `.env`.

## Comandi locali
Per allineare il database locale Laragon:
- `php artisan migrate`
- `php artisan db:seed`

Se si vuole riallineare da zero l'ambiente locale di sviluppo:
- `php artisan migrate:fresh --seed`

## Come verificare
1. Avviare Apache e MySQL da Laragon.
2. Eseguire le migrazioni e il seed.
3. Autenticarsi nel portale bootstrap.
4. Verificare che il contesto attivo non sia piu' solo placeholder globale.
5. Verificare API:
   - `GET /api/v1/me`
   - presenza di `data.context.active_organization`
   - presenza di `data.context.active_membership`

## Attenzioni
- `organizations` in questa fase non rappresenta ancora dealer, compagnia o struttura organization finale.
- La membership introdotta e' foundation tecnica del nuovo portale, non modello di dominio completo.
- Il prossimo step dovrebbe restare su refinement del context e non aprire ancora catalogo o pratiche.
