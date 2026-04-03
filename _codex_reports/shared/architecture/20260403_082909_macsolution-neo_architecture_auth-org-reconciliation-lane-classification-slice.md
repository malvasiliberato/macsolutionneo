# Contesto
Dopo l'audit semantico legacy e il refinement dei lane AUTH/ORG, il filone import aveva bisogno di rendere questa distinzione visibile anche nel reconciliation layer reale, non solo nei documenti.

## Obiettivo del task
Implementare un primo `lane-classification slice` nel dry-run AUTH/ORG, mantenendo invariati commit gate e runtime applicativo.

## Cosa e' stato implementato davvero
- `DefaultAuthOrgLegacyCandidateResolver` ora assegna:
  - `candidate_lane`
  - `candidate_lane_subtype`
- `AuthOrgLegacyReconciliationReportBuilder` ora espone:
  - `lane_breakdown`
- Il comando `legacy:import:auth-org --dry-run` mostra quindi il breakdown per lane semantico oltre al breakdown per entita'.

## Lane coperti
- `internal_platform_principal`
- `dealer_organization`
- `dealer_seller`
- `dealer_operator`
- `unknown` come lane transitorio e dichiarato

## Regola applicata
Il reconciliation layer distingue ora prima:
1. il lane semantico del candidato
2. poi lo stato operativo (`ready_link`, `ready_create`, `needs_review`, `blocked`)

## Nota sul lane `unknown`
- alcuni record bootstrap come `dealer_user_assignment` non sono ancora ricondotti a un lane definitivo;
- vengono quindi lasciati esplicitamente in `unknown` invece di forzare una classificazione impropria.

## Verifiche
- test mirati del comando AUTH/ORG:
  - `php artisan test --filter=AuthOrgLegacyImportCommandTest`
  - esito: verde

## File aggiornati
- `app/Application/Auth/LegacyImport/DefaultAuthOrgLegacyCandidateResolver.php`
- `app/Application/Auth/LegacyImport/AuthOrgLegacyReconciliationReportBuilder.php`
- `tests/Feature/Console/AuthOrgLegacyImportCommandTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Cosa e' stato volutamente rimandato
- nessun nuovo commit import
- nessuna modifica al gate `create-only`
- nessun redesign del matching
- nessun cambiamento runtime lato portale

## Rischi residui
- il lane `unknown` va ridotto nei prossimi slice di refinement
- il lane `internal_platform_principal` richiede ancora decisioni manuali sui casi umani/corporate ambigui

## Raccomandazione operativa finale
Il prossimo macro-step corretto e' un refinement dei record ancora `unknown` o residuali nel filone AUTH/ORG, prima di qualunque nuovo widening del commit gate.
