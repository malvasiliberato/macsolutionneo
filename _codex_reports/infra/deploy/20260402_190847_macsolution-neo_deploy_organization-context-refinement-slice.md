# Mac Solution Neo - Deploy Notes Organization Context Refinement Slice

## File toccati
- `app/Support/Auth/ResolveAuthenticatedPortalContext.php`
- `tests/Feature/Api/MeTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Impatto setup locale
- Nessuna nuova migrazione.
- Nessun nuovo seed.
- Nessuna modifica a `.env`, Laragon o build frontend.

## Note operative
- Il contract `context` espone ora anche:
  - `available_memberships`
  - `context_switching`
- Lo switching resta read-only in questa fase.
- La regola attiva e' `primary_membership_first`.

## Verifica locale
- `php artisan test` deve restare verde.
- `GET /api/v1/me` deve esporre metadata coerenti su membership multiple.

## Attenzione
- Il command mirato `php artisan test --filter=MeTest` puo' risultare sensibile al lifecycle del database MySQL di test nel contesto Laragon.
- La suite completa e' il riferimento di verifica usato per questo slice.
