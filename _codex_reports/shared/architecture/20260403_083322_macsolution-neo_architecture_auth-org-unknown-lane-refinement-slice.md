# Contesto
Nel filone `AUTH/ORG` il reconciliation layer espone gia' `candidate_lane`, `candidate_lane_subtype` e `lane_breakdown`.

Dopo il lane-classification slice restava pero' un residuo semantico non desiderato: alcuni record bootstrap di `dealer_user_assignment` continuavano a comparire nel lane `unknown`, pur essendo ormai comprensibili come bridge operativo e non come attori senza semantica.

# Obiettivo del task
Ridurre l'area grigia del reconciliation layer riclassificando i bridge legacy gia' capiti, senza toccare il gate `create-only`, senza aprire nuovi commit e senza allargare il modello runtime.

# Problema rilevato
- Nel dataset `bootstrap-auth-org-create-only` il `lane_breakdown` mostrava ancora `unknown = 1`.
- Il record residuo era:
  - `legacy_table = dealer_user_assignment`
  - `candidate_type = membership`
  - `legacy_user_email = operator@dealer-gamma.test`
  - `legacy_organization_code = dealer-gamma`
- Questo record era gia' trattato come `ready_create`, quindi lasciarlo in `unknown` rendeva incoerente la lettura semantica del bootstrap.

# Decisione adottata
- `dealer_user_assignment` viene ora ricondotto in modo esplicito al lane `dealer_operator`.
- La classificazione usa:
  - `candidate_lane = dealer_operator`
  - `candidate_lane_subtype` derivato da `legacy_assignment_role_code` oppure `target_membership_role_code`

# Motivazione
- `dealer_user_assignment` non e' una sorgente primaria di identita' dealer-seller.
- E' un bridge legacy che collega un account o operatore a un dealer/organization.
- Mantenerlo in `unknown` avrebbe lasciato aperta un'ambiguita' gia' risolta a livello semantico.

# Cosa e' stato implementato davvero
- Aggiornato `DefaultAuthOrgLegacyCandidateResolver` per classificare `dealer_user_assignment` come `dealer_operator`.
- Aggiornato il test bootstrap sul `lane_breakdown`.
- Aggiornati `AI_CONTEXT.md` e `MASTER_PROGRESS.md` per fissare il principio:
  - il lane `unknown` deve restare solo fallback per record davvero non classificati.

# Cosa e' stato volutamente rimandato
- Nessun cambiamento al matcher.
- Nessun cambiamento al gate `create-only`.
- Nessun nuovo commit reale.
- Nessun widening sui residui `internal_platform_principal`.

# Verifica
- `php artisan test --filter=AuthOrgLegacyImportCommandTest`
  - esito: `9 passed`
- `php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=bootstrap-auth-org-create-only --batch=20`
  - `lane_breakdown` aggiornato:
    - `internal_platform_principal = 1`
    - `dealer_organization = 1`
    - `dealer_seller = 2`
    - `dealer_operator = 4`
    - nessun `unknown`

# Risultato finale
- Il bootstrap `AUTH/ORG` non lascia piu' bridge legacy noti in `unknown`.
- Il lane `unknown` resta disponibile solo come fallback dichiarato per casi davvero non ancora compresi.

# Implicazione per i prossimi step
Il prossimo refinement utile non e' piu' sui bridge noti, ma sui residui `internal_platform_principal` o su eventuali futuri record realmente non classificabili senza audit aggiuntivo.
