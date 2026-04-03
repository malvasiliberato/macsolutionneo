# Contesto
Nel filone `AUTH/ORG` il reconciliation layer distingue gia' i lane semantici principali:
- `internal_platform_principal`
- `dealer_organization`
- `dealer_seller`
- `dealer_operator`

Dopo il lane refinement e la chiusura del residuo `unknown`, restava pero' un problema di leggibilita': il lane `internal_platform_principal` era visibile come aggregato, ma non sempre esponeva bene i sottotipi reali o i casi ancora non abbastanza precisi.

# Obiettivo del task
Rendere piu' leggibile il lane `internal_platform_principal` nel dry-run e nel reporting, senza modificare il gate `create-only`, senza nuove scritture e senza aprire widening del reconciliation layer.

# Problema rilevato
- Il report tecnico esponeva `candidate_lane_subtype`, ma non lo aggregava in modo leggibile.
- Nel dataset bootstrap `bootstrap-auth-org-create-only` il principal interno `dealer.gamma.admin` entrava correttamente nel lane `internal_platform_principal`, ma non aveva un sottotipo esplicito.
- Questo rendeva opaca la lettura del lane admin/backoffice.

# Decisioni adottate
- Il reconciliation report espone ora `lane_subtype_breakdown`.
- I principal interni non ancora abbastanza precisi vengono classificati con il fallback:
  - `unclassified_internal_principal`
- Questo fallback vale solo per il lane `internal_platform_principal`, senza cambiare le regole di matching o review.

# Cosa e' stato implementato davvero
- Aggiornato `DefaultAuthOrgLegacyCandidateResolver`:
  - fallback subtype per principal interni non riconosciuti => `unclassified_internal_principal`
- Aggiornato `AuthOrgLegacyReconciliationReportBuilder`:
  - aggiunto `lane_subtype_breakdown`
  - breakdown per sottotipo con conteggi `ready_link`, `ready_create`, `needs_review`, `blocked`
- Aggiornati i test sul report tecnico bootstrap.
- Aggiornati `AI_CONTEXT.md` e `MASTER_PROGRESS.md`.

# Cosa e' stato volutamente rimandato
- Nessuna modifica al matcher.
- Nessuna modifica al commit gate.
- Nessuna nuova policy sui principal interni.
- Nessuna nuova scrittura sul dominio Neo.

# Verifica
```powershell
php artisan test --filter=AuthOrgLegacyImportCommandTest
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=bootstrap-auth-org-create-only --batch=20
```

Esito:
- test verdi: `9 passed`
- `lane_subtype_breakdown` ora espone anche:
  - `internal_platform_principal.unclassified_internal_principal`
  - `dealer_operator.dealer_operator`
  - `dealer_operator.dealer_seller`
  - `dealer_seller.dealer_seller`

# Risultato finale
- Il lane `internal_platform_principal` non e' piu' solo un contenitore aggregato.
- I casi interni non ancora abbastanza puliti restano visibili e dichiarati, invece di perdersi nel breakdown.

# Implicazione per i prossimi step
Il passo successivo utile e' lavorare sui sottotipi residui reali del lane `internal_platform_principal`, usando il nuovo breakdown per separare:
- casi umani plausibili
- casi tecnici/test
- casi corporate o comunque non promuovibili automaticamente
