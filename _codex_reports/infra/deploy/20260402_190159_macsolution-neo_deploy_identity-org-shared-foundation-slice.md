# Mac Solution Neo - Deploy Notes Identity Org Shared Foundation Slice

## File toccati
- `app/Support/Auth/ResolveAuthenticatedPortalContext.php`
- `app/Application/Portal/Navigation/BuildPortalNavigation.php`
- `app/Http/Controllers/Api/V1/MeController.php`
- `app/Http/Resources/Api/V1/CurrentUserResource.php`
- `app/Http/Middleware/HandleInertiaRequests.php`
- `config/portal.php`
- `resources/js/Layouts/AuthenticatedLayout.vue`
- `tests/Feature/Api/MeTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Setup locale
- Nessuna nuova dipendenza introdotta.
- Nessuna nuova migrazione richiesta in questo step.
- Nessuna variazione richiesta a `.env`, Laragon, host o database locale.

## Come verificare in locale
1. Avviare Apache e MySQL da Laragon.
2. Nel workspace eseguire:
   - `php artisan test`
   - `npm.cmd run build`
3. Verificare da portale autenticato:
   - topbar con label contesto bootstrap
   - navigazione invariata nella sostanza
4. Verificare API autenticata:
   - `GET /api/v1/me`
   - presenza di `data.context`

## Attenzioni operative
- `context` e' intenzionalmente placeholder: non va scambiato per organization model definitivo.
- La navigation metadata e' stata resa piu' stabile, ma senza introdurre logica modulo-specifica.
- Il prossimo step puo' introdurre persistence minima solo se resta confinato alla foundation auth/org.

## Risultato operativo
Il workspace resta avviabile nel contesto Windows + Laragon e guadagna una base shared piu' solida per evolvere auth/org senza widening.
