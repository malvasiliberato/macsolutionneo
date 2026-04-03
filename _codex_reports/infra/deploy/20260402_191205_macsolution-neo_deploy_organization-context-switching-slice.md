# Mac Solution Neo - Deploy Notes Organization Context Switching Slice

## File toccati
- `app/Http/Controllers/Api/V1/SetActiveMembershipController.php`
- `app/Support/Auth/ResolveAuthenticatedPortalContext.php`
- `app/Http/Controllers/Api/V1/MeController.php`
- `app/Http/Middleware/HandleInertiaRequests.php`
- `config/portal.php`
- `routes/api.php`
- `tests/Feature/Api/MeTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Impatto setup locale
- Nessuna nuova migrazione.
- Nessun nuovo seed.
- Nessuna modifica host/config Laragon.

## Endpoint introdotto
- `POST /api/v1/context/active-membership`
  - payload: `membership_id`
  - auth: `auth:sanctum`
  - effetto: salva in sessione la membership attiva se valida

## Verifica locale
- autenticarsi nel portale
- usare l'endpoint con una membership valida dell'utente
- verificare poi `GET /api/v1/me`
- confermare:
  - `context.active_membership`
  - `context.context_switching.strategy = session_selected_membership`

## Attenzione
- Lo switching e' attualmente session-based e pensato per il bootstrap web/stateful.
- Non costituisce ancora un contract definitivo per client mobile stateless.
