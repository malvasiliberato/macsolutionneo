# Contesto
Il filone `AUTH/ORG` espone gia' nel dry-run:
- `candidate_lane`
- `candidate_lane_subtype`
- `lane_breakdown`
- `lane_subtype_breakdown`

Restava pero' un'esigenza operativa: leggere i residui `internal_platform_principal` in categorie piu' utili per la review, senza toccare matching o commit gate.

# Obiettivo del task
Separare meglio i residui `internal_platform_principal` in categorie operative leggibili:
- `human_plausible`
- `technical_or_test`
- `corporate`

senza riaprire il gate `create-only` e senza introdurre nuove scritture.

# Problema rilevato
- Il `lane_subtype_breakdown` aiuta gia' la lettura, ma richiede ancora interpretazione manuale.
- Per la review dei principal interni serve una vista piu' sintetica e orientata alle decisioni.

# Decisione adottata
E' stato introdotto nel reconciliation snapshot un nuovo breakdown:
- `internal_principal_category_breakdown`

Regola di categorizzazione:
- `technical_or_test` => `technical_or_test`
- `corporate_ambiguous` => `corporate`
- tutti gli altri sottotipi interni attuali => `human_plausible`

Questa regola e' volutamente prudente e serve solo per reporting e review.

# Cosa e' stato implementato davvero
- Aggiornato `AuthOrgLegacyReconciliationReportBuilder`
  - nuovo campo `internal_principal_category_breakdown`
- Aggiornati i test del dry-run bootstrap
- Aggiornati `AI_CONTEXT.md` e `MASTER_PROGRESS.md`

# Cosa e' stato volutamente rimandato
- Nessuna modifica al matcher
- Nessuna modifica al commit gate
- Nessun cambiamento a create-only
- Nessuna nuova policy di auto-import sui principal interni

# Verifica
```powershell
php artisan test --filter=AuthOrgLegacyImportCommandTest
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=bootstrap-auth-org-create-only --batch=20
```

Esito:
- test verdi: `9 passed`
- il dry-run espone ora:
  - `internal_principal_category_breakdown={"human_plausible":{"ready_link":0,"ready_create":0,"needs_review":1,"blocked":0}}`

# Risultato finale
- I residui `internal_platform_principal` non sono piu' leggibili solo per lane o sottotipo.
- Il reconciliation report li separa ora anche per categoria operativa di review.

# Implicazione per i prossimi step
Il prossimo macro-step utile e' lavorare sui casi `human_plausible` del lane `internal_platform_principal`, usando il nuovo breakdown per distinguere:
- casi da review umana plausibile;
- casi tecnici/test da non migrare;
- casi corporate da trattare come target decision separata.
