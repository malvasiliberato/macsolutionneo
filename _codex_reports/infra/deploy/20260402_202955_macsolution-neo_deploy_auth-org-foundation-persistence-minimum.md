# File creati o modificati
- `app/Models/DealerOperatorAssignment.php`
- `app/Models/Organization.php`
- `app/Models/OrganizationMembership.php`
- `app/Models/User.php`
- `app/Support/Auth/ResolveAuthenticatedPortalContext.php`
- `app/Support/Auth/CurrentUserCapabilities.php`
- `app/Http/Resources/Api/V1/CurrentUserResource.php`
- `app/Http/Middleware/HandleInertiaRequests.php`
- `config/portal.php`
- `database/migrations/2026_04_02_203100_add_auth_org_foundation_bridge_tables.php`
- `database/seeders/DatabaseSeeder.php`
- `tests/Feature/Api/MeTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# Migrazioni introdotte
- `2026_04_02_203100_add_auth_org_foundation_bridge_tables.php`
  - aggiunge `parent_organization_id` a `organizations`
  - crea `dealer_operator_assignments`

# Seed introdotti o aggiornati
- Seed bootstrap minimo aggiornato con:
  - `dealer-bootstrap` come organization figlia del workspace bootstrap
  - un assignment minimo `dealer_account_manager`
  - mapping tecnici legacy bootstrap coerenti con il nuovo foundation slice

# Impatto runtime
- Reale ma contenuto al foundation auth/org.
- `/api/v1/me` e Inertia shared leggono ora:
  - `account_status`
  - `active_organization.parent_organization`
  - `context.assignments`
- Nessuna feature business nuova introdotta.

# Come verificare localmente
- Eseguire:
  - `php artisan migrate:fresh --seed`
  - `php artisan test`
- Verificare che la suite completa sia verde e che `MeTest` passi con bridge organizzativo e assignments attivi.

# Note Laragon/local setup
- Nessuna nota speciale oltre al setup Laragon gia' in uso.
- La suite completa `php artisan test` resta il riferimento piu' affidabile in questo workspace, piu' dei filtri mirati, per il noto lifecycle MySQL/Laragon dei test con `migrate:fresh`.
