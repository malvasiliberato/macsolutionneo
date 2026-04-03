# Mac Solution Neo - Deploy Notes Scoped Capability Resolution Slice

## File toccati
- `app/Support/Auth/CurrentUserCapabilities.php`
- `app/Application/Portal/Navigation/BuildPortalNavigation.php`
- `app/Http/Controllers/Api/V1/MeController.php`
- `app/Http/Controllers/Api/V1/SetActiveMembershipController.php`
- `app/Http/Middleware/HandleInertiaRequests.php`
- `config/portal.php`
- `resources/js/Layouts/AuthenticatedLayout.vue`
- `tests/Feature/Api/MeTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Impatto setup locale
- Nessuna nuova migrazione.
- Nessun nuovo seed.
- Nessuna variazione richiesta a `.env` o Laragon.

## Come verificare
- `php artisan test`
- `npm.cmd run build`
- autenticarsi nel portale
- verificare `GET /api/v1/me`
- verificare che il cambio di membership attiva possa influenzare:
  - `data.capabilities`
  - `data.context`

## Attenzione
- La risoluzione capability e' ora context-aware ma resta volutamente minimale.
- Non sostituisce ancora una futura ACL finale scoped di dominio.
