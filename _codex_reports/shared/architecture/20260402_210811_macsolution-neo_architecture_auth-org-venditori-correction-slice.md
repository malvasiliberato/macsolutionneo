# Auth/Org Venditori Correction Slice

## Contesto
Il foundation `AUTH/ORG` di Mac Solution Neo aveva gia' una persistence minima reale con:
- `users`
- `organizations`
- `organization_memberships`
- `dealer_operator_assignments`

Nel filone import/reconciliation erano gia' presenti:
- `legacy_entity_mappings`
- dry-run read-only
- matching per `user / organization / membership / assignment`

La lacuna emersa e' che i venditori del dealer non erano ancora esplicitati abbastanza come attori identitari di primo livello del foundation model.

## Problema rilevato
Nel legacy i venditori del dealer non sono un dettaglio secondario del dealer-organization, ma utenti reali con proprie credenziali.

Senza questa correzione il foundation Neo rischiava di:
- trattarli implicitamente come dettaglio organizzativo;
- lasciare ambiguo il mapping `dealer_collaboratore -> Neo`;
- arrivare al prossimo dry-run reale con una lacuna proprio nel boundary `identity + membership`.

## Evidenze legacy sui venditori
Audit mirato in sola lettura sul DB legacy locale `sql1483615_1`:
- tabella coinvolta: `dealer_collaboratore`
- campi chiave osservati:
  - `id`
  - `id_dealer`
  - `nominativo`
  - `username`
  - `password`
  - `password_value`
  - `ruolo`
  - `email`
  - `is_enable`
- volume osservato: `668` record
- con credenziali:
  - `668/668` con `username`
  - `668/668` con `password`
- ruoli legacy osservati:
  - `collaboratore`: `361`
  - `amministratore`: `307`
- dealer distinti referenziati: `277`
- stato `is_enable`:
  - `1`: `441`
  - `0`: `227`

Queste evidenze confermano che `dealer_collaboratore` rappresenta utenti autenticabili dealer-scoped.

## Correzione del modello target
Correzione consolidata nel foundation Neo:
- il `dealer` resta una `organization`;
- il venditore del dealer e' un `user` autenticabile;
- il legame tra venditore e dealer passa per `organization_memberships`;
- il layer `dealer_operator_assignments` resta separato e non modella l'identita' del venditore.

Scelta minima ma esplicita sul ruolo di membership:
- `dealer_collaboratore.ruolo = collaboratore` -> `organization_memberships.role_code = dealer_seller`
- `dealer_collaboratore.ruolo = amministratore` -> `organization_memberships.role_code = dealer_admin`

Questa correzione distingue i venditori sia dal dealer-organizzazione sia da figure future tipo `commerciale/account/backoffice`.

## Modifiche applicate
- `config/portal.php`
  - aggiunti i ruoli membership `dealer_seller` e `dealer_admin`
- `database/seeders/DatabaseSeeder.php`
  - aggiunto un account bootstrap reale di venditore dealer
  - aggiunta membership primaria sul dealer bootstrap
  - aggiunti mapping tecnici bootstrap da `dealer_collaboratore` verso `user` e `organization_membership`
- `app/Application/Auth/LegacyImport/MySqlAuthOrgLegacyDatasetAdapter.php`
  - il source `dealer_collaboratore` espone ora in modo esplicito:
    - `legacy_role_code`
    - `legacy_dealer_code`
    - `target_membership_role_code`
- `app/Application/Auth/LegacyImport/AuthOrgLegacyDryRun.php`
  - il dataset controllato `bootstrap-auth-org` include ora anche il caso venditore dealer
- `app/Application/Auth/LegacyImport/DefaultAuthOrgLegacyCandidateResolver.php`
  - reason piu' esplicite per i candidati derivati da `dealer_collaboratore`
- `tests/Feature/Api/MeTest.php`
  - aggiunto test sul caso venditore come attore autenticato di primo livello
- `tests/Feature/Console/AuthOrgLegacyImportCommandTest.php`
  - riallineate le attese del dry-run bootstrap con il caso venditore incluso

## Impatto su persistence, membership e shared contract
Schema:
- nessuna nuova migrazione e nessuna nuova tabella
- la persistence minima esistente era gia' sufficiente; serviva renderne esplicito l'uso corretto

Membership:
- il venditore dealer ora e' rappresentato chiaramente come membership dealer-scoped
- la distinzione `identity / membership / assignment` resta intatta

Shared contract:
- nessun redesign del contract
- `/api/v1/me` rappresenta correttamente il caso venditore tramite:
  - `context.active_organization.type = dealer`
  - `context.active_membership.role_code = dealer_seller`
  - `context.active_organization.parent_organization` quando presente

## Mapping legacy -> Neo aggiornato
- `dealer`
  - quando rappresenta davvero il soggetto commerciale/istituzionale, resta candidato a `organizations`
- `dealer_collaboratore`
  - `user` candidato a `users`
  - membership candidato a `organization_memberships` del dealer
  - `collaboratore` -> `dealer_seller`
  - `amministratore` -> `dealer_admin`
- `commerciali`
  - restano attori da trattare con prudenza come figure operative distinte
- `dealer_operatore_figura`
  - resta sorgente per `dealer_operator_assignments`, non per modellare l'identita' base del venditore

## Gap o ambiguita' ancora aperte
- molte righe `dealer_collaboratore` nel dataset reale hanno email mancanti o placeholder
- il ruolo legacy `collaboratore` non equivale ancora a una ACL finale, ma solo a un primo `membership role_code`
- il rapporto preciso tra venditore dealer e figure `commerciale/account` resta da affinare nei prossimi slice di signal hardening
- questa correzione non abilita ancora commit import reale

## Perche' questa correzione e' necessaria adesso
Serve prima dei prossimi step perche':
- evita un dry-run reale auth/org con una lacuna proprio sugli utenti dealer;
- chiarisce che il dealer non e' il principal unico del canale dealer;
- rende piu' affidabile la futura riconciliazione `legacy -> Neo` su account e membership;
- mantiene il foundation mobile-ready e backend-first senza introdurre moduli business.

## Verifiche eseguite
- `php artisan migrate:fresh --seed`
- `php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=bootstrap-auth-org --batch=20`
- `php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=dealer_collaboratore --dataset=bootstrap-auth-org --batch=20`
- `php artisan test`

## Esito
La correzione e' sufficiente e coerente per considerare i venditori dealer come first-class authenticated actors del foundation `AUTH/ORG`.

Il filone e' ora piu' pronto a proseguire con il prossimo dry-run reale senza questa lacuna strutturale.
