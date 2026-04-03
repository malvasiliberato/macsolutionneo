# Contesto
Nel filone `AUTH/ORG` il dry-run espone gia':
- `lane_breakdown`
- `lane_subtype_breakdown`
- `internal_principal_category_breakdown`

Per il lane `internal_platform_principal` serviva pero' un passo ulteriore: tradurre la lettura semantica in una vista direttamente utile alla review operativa.

# Obiettivo del task
Rendere i residui `internal_platform_principal` leggibili con esiti operativi suggeriti, senza toccare:
- matching
- commit gate
- create-only
- import reale

# Decisione adottata
E' stato introdotto nel reconciliation snapshot:
- `internal_principal_review_action_breakdown`

Mappatura prudenziale adottata:
- `supporto_tecnico` => `manual_link_candidate`
- `superadmin` => `manual_review_only`
- `backoffice_operativo` => `manual_review_only`
- `unclassified_internal_principal` => `manual_review_only`
- `technical_or_test` => `do_not_migrate_automatically`
- `corporate_ambiguous` => `manual_target_decision_required`

# Motivazione
- Il lane `internal_platform_principal` non deve restare una massa indistinta di review.
- Serve una vista che dica subito come trattare i residui:
  - candidati plausibili da link manuale;
  - casi solo da review;
  - casi tecnici da non promuovere;
  - casi corporate da target decision separata.

# Cosa e' stato implementato davvero
- Aggiornato `AuthOrgLegacyReconciliationReportBuilder`
  - nuovo breakdown `internal_principal_review_action_breakdown`
- Esteso `AuthOrgLegacyDryRun` con dataset controllato:
  - `bootstrap-auth-org-internal-principal-review`
- Aggiornati i test del comando dry-run
- Aggiornati `AI_CONTEXT.md` e `MASTER_PROGRESS.md`

# Cosa e' stato volutamente rimandato
- Nessun cambiamento al matcher
- Nessuna nuova scrittura
- Nessuna modifica al gate `create-only`
- Nessuna decisione finale automatica sui principal interni reali

# Verifica
```powershell
php artisan test --filter=AuthOrgLegacyImportCommandTest
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=bootstrap-auth-org-internal-principal-review --batch=20
```

Esito:
- test verdi: `10 passed`
- il dry-run espone:
  - `internal_principal_review_action_breakdown={"do_not_migrate_automatically":{"ready_link":0,"ready_create":0,"needs_review":1,"blocked":0},"manual_link_candidate":{"ready_link":0,"ready_create":0,"needs_review":1,"blocked":0},"manual_review_only":{"ready_link":0,"ready_create":0,"needs_review":2,"blocked":0},"manual_target_decision_required":{"ready_link":0,"ready_create":0,"needs_review":1,"blocked":0}}`

# Risultato finale
- I principal interni residui possono ora essere letti direttamente come backlog di review operativa.
- Il lane `internal_platform_principal` resta fuori dal commit automatico, ma e' molto piu' governabile.

# Implicazione per i prossimi step
Il prossimo step sensato e' lavorare sui casi `manual_link_candidate` e `manual_review_only`, partendo da:
- `supporto`
- `admin`
- `rosy`
senza riaprire il gate automatico.
