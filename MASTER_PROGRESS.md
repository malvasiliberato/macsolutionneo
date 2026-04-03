# MASTER_PROGRESS.md

## Contesto
Questa plancia sintetica governa l'avanzamento del workspace Mac Solution Neo. Serve a indicare stato generale, filoni attivi, dipendenze logiche e focus consigliato, senza trasformarsi in backlog dispersivo.

## Stato generale
- Governance documentale di bootstrap: `validato`
- Governance legacy import -> Neo: `validato`
- Portal bootstrap Laravel + Vue: `validato`
- Bootstrap consolidation leggero workspace: `validato`
- Implementazione moduli business: `non avviata`
- Audit strutturati del legacy in questo workspace: `avviati`
- Fondazioni architetturali condivise: `chiarite a livello design`

## Principi di avanzamento
- Step piccoli e verificabili
- Granularita' umana e governabile, non microscopica
- Stato esplicito per ogni step
- Distinzione netta tra audit, design, implementazione, e2e, deploy
- Nessun widening oltre il perimetro richiesto
- Legacy osservato come baseline funzionale, non come modello architetturale
- Ogni bounded context con impatto dati deve lasciare traccia di schema target, mapping legacy -> target e strategia di import

## Filoni principali
- Governance workspace
- Governance legacy import -> Neo
- Versionamento Git e deploy progressivi
- Bootstrap tecnico portale
- Audit legacy per bounded context
- Foundation identity + organization
- Foundation catalogo
- Shared capability: API, permessi, assegnazioni, sidebar
- Moduli business progressivi
- E2E dei flussi critici
- Setup e deploy progressivi

## Nota operativa trasversale
- Il workspace e' ora predisposto anche per:
  - versionamento Git locale;
  - auto deploy prudente su Plesk tramite script `scripts/deploy/plesk/post-deploy.sh`;
  - senza introdurre ancora pipeline CI/CD piu' pesanti del necessario.

## Step iniziali raccomandati
| Step ID | Titolo breve | Tipo | Stato | Nota |
| --- | --- | --- | --- | --- |
| MSN-GOV-001 | Workspace governance bootstrap | design | validato | Governance minima introdotta in root e reportistica |
| MSN-GOV-003 | Legacy import governance foundation | design | validato | Regole permanenti introdotte per schema target, mapping, import strategy e tracking legacy references |
| MSN-DEP-001 | Portal bootstrap Laravel Vue | implementazione | validato | Base tecnica reale con auth, API foundation, shell UI Velzon e migrazioni minime |
| MSN-GOV-002 | Bootstrap consolidation | implementazione | validato | Workspace ripulito dagli artefatti temporanei e naming ambiguo del bootstrap |
| MSN-AUD-001 | Audit legacy identity and organization | audit | validato | Baseline funzionale consolidata: ingressi distinti, ACL ibrida e assignment dealer/commerciali distribuiti |
| MSN-AUTH-001 | Identity foundation target model | design | validato | Target model iniziale confermato dall'audit: account distinto da membership, assignment e capability |
| MSN-ORG-001 | Organization foundation target model | design | validato | Boundary iniziale confermato dall'audit: organization, membership, unit/scope e assignment non coincidono con user |
| MSN-CAT-001 | Audit legacy catalog foundation | audit | backlog | Da impostare dopo chiarimento foundation auth/org |
| MSN-SHARED-001 | Capability and sidebar governance | design | validato | Contratto target chiarito per capability, `/api/v1/me`, navigazione backend-driven e scope futuri |
| MSN-AUTH-002 | Identity-org shared foundation slice | implementazione | validato | Primo slice tecnico introdotto: context condiviso bootstrap-safe, `me` evolutivo e navigation metadata piu' stabili |
| MSN-AUTH-003 | Membership persistence bootstrap slice | implementazione | validato | Persistence minima introdotta per `organizations` e `organization_memberships`, con context attivo agganciato a membership |
| MSN-ORG-002 | Organization context refinement slice | design | validato | Regola di selezione del context chiarita: primary membership first, metadata read-only di context switching e memberships disponibili |
| MSN-ORG-003 | Organization context switching slice | implementazione | validato | Switching minimo introdotto via sessione autenticata e endpoint API, con validazione ownership membership |
| MSN-SHARED-002 | Scoped capability resolution slice | implementazione | validato | Capability e navigation rese sensibili al context attivo, con mapping membership-aware e scope navigation |
| MSN-CAT-001 | Audit legacy catalog foundation | audit | validato | Baseline funzionale consolidata: catalogo legacy centrato su compagnie abilitate, garanzie, tariffe, aree e dispositivi |
| MSN-CAT-002 | Catalog target model foundation | design | validato | Target model iniziale chiarito: supplier, company, product, coverage, pricing profile e dealer enablement separati |
| MSN-CAT-003 | Catalog foundation implementation slice | implementazione | validato | Primitive minime persistence introdotte per supplier, company, product, coverage e relazioni base |
| MSN-CAT-004 | Catalog pricing-enablements design slice | design | validato | Boundary iniziale chiarito tra pricing profile e dealer enablement, con agganci raccomandati a company/product/coverage e organization/dealer |
| MSN-CAT-005 | Catalog pricing-enablements implementation slice | implementazione | validato | Primo slice tecnico di dealer enablement introdotto tra organization e product, senza pricing engine |
| MSN-CAT-006 | Catalog pricing profile design slice | design | ready | Chiarire il primo target model minimale del pricing profile, senza aprire ancora engine di quotazione |
| MSN-AUTH-004 | Identity legacy import mapping foundation | design | validato | Primo pacchetto esplicito definito per schema target, mapping legacy -> target, strategia di import e gap auth/org |
| MSN-AUTH-005 | Identity legacy reference persistence design | design | validato | Chiarito dove usare `legacy_id` semplice e dove invece servono mapping table tecniche per account, organization e membership |
| MSN-AUTH-006 | Identity legacy mapping persistence slice | implementazione | validato | Persistence tecnica minima introdotta con `legacy_entity_mappings`, separata dal dominio e pronta per import/reimport futuri |
| MSN-AUTH-007 | Identity legacy import command design | design | validato | Contratto minimo chiarito per comando auth/org con input attesi, dry-run, output tecnico e regole di idempotenza |
| MSN-AUTH-008 | Identity legacy import dry-run slice | implementazione | validato | Primo comando tecnico `legacy:import:auth-org --dry-run` introdotto con dataset controllato e output read-only verificabile |
| MSN-AUTH-009 | Identity legacy import dataset adapter design | design | validato | Contratto chiarito per adapter legacy reale read-only, mantenendo il comando stabile e il dataset controllato come fallback |
| MSN-AUTH-010 | Identity legacy import dataset adapter slice | implementazione | validato | Primo adapter legacy reale read-only introdotto dietro il comando auth/org con fallback controllato preservato |
| MSN-AUTH-011 | Identity legacy candidate resolution design | design | validato | Chiarito il primo layer read-only di candidate resolution sopra l'adapter con type, status e reason espliciti |
| MSN-AUTH-012 | Identity legacy candidate resolution slice | implementazione | validato | Primo resolver read-only introdotto e agganciato al dry-run auth/org con metriche e reason piu' leggibili |
| MSN-AUTH-013 | Identity legacy candidate matching design | design | validato | Chiarito il primo livello read-only di matching con `match_status` e `match_reason` sopra i candidate risolti |
| MSN-AUTH-014 | Identity legacy candidate matching slice | implementazione | validato | Primo matcher read-only introdotto e agganciato al dry-run auth/org con metriche di matching verso target Neo esistenti |
| MSN-AUTH-015 | Identity legacy matching heuristics design | design | validato | Chiarite le prime famiglie di segnali e le regole di prudenza per il matching read-only oltre il bootstrap statico |
| MSN-AUTH-016 | Identity legacy matching heuristics slice | implementazione | validato | Prime euristiche concrete introdotte nel matcher read-only auth/org con metriche dedicate su heuristic vs mapping matches |
| MSN-AUTH-017 | Identity legacy matching edge-cases design | design | validato | Chiarite collisioni, segnali deboli, casi ambigui e stop conditions del matching read-only auth/org |
| MSN-AUTH-018 | Identity legacy matching edge-cases slice | implementazione | validato | Prima gestione concreta degli edge case introdotta nel matcher read-only auth/org con ambiguous match e reconciliation batch result |
| MSN-AUTH-019 | Identity legacy matching confidence design | design | validato | Primo livello di confidence scoring read-only chiarito per distinguere match forti, medi, deboli e non affidabili |
| MSN-AUTH-020 | Identity legacy matching confidence slice | implementazione | validato | Confidence scoring introdotto nel dry-run auth/org con `match_confidence` e metriche aggregate senza aprire commit mode |
| MSN-AUTH-022 | Auth-org foundation persistence minimum | implementazione | validato | Persistence minima reale consolidata con bridge organizzativo, membership persistite, assignment dealer -> operatore e contract shared allineato |
| MSN-AUTH-021 | Auth-org import reconciliation foundation | implementazione | validato | Foundation tecnica consolidata con dry-run, snapshot di reconciliation, chiavi di matching esplicite e supporto anche per assignment |
| MSN-AUTH-024 | Auth-org import dry-run real dataset | audit | validato | Dry-run eseguito su `sql1483615_1`: 1621 record letti, 595 `ready_create`, 1026 casi da review e commit attuale `NO-GO` |
| MSN-AUTH-026 | Auth-org venditori correction slice | implementazione | validato | Dealer seller corretti come account autenticabili di primo livello: `dealer_collaboratore` ricondotto a `users` + `organization_memberships` dealer-scoped |
| MSN-AUTH-025 | Auth-org signal hardening design | implementazione | validato | Placeholder identity filtrati, dealer seller dealer-scoped trattati meglio e dealer admin mantenuti in review prudenziale |
| MSN-AUTH-027 | Auth-org import dry-run second pass | audit | validato | Secondo dry-run reale eseguito: 1604 `ready_create`, 17 review residue, miglioramento sostanziale e stato `GO condizionato` al commit gate |
| MSN-AUTH-023 | Auth-org import commit gate design | implementazione | validato | Gate create-only introdotto: commit solo su `ready_create`, review escluse, tracing idempotente su `legacy_entity_mappings` e nessun merge aggressivo |
| MSN-AUTH-028 | Auth-org create-only commit first pass | implementazione | validato | Primo commit controllato reale eseguito su `sql1483615_1`: 1351 mapping create-only, 654 users, 272 organizations, 425 memberships, 1 assignment, 16 review escluse |
| MSN-AUTH-029 | Auth-org post-create-only reconciliation follow-up | audit | validato | Residui consolidati: 16 dealer admin in review, 3 candidati create-only ancora dipendenti da membership/assignment mancanti, 12 conflitti unici osservati nel primo passaggio |
| MSN-AUTH-030 | Auth-org create-only dependency guard slice | implementazione | validato | Reconciliation riallineata al gate reale: i 3 falsi `ready_create` residui sono stati declassati a review (`1` membership, `2` assignment), lasciando `0` ready_create e `19` review reali nel dry-run |
| MSN-AUTH-031 | Auth-org residual review baseline | audit | validato | Residui reali consolidati: `16` dealer admin legacy senza identita' forte, `1` membership dealer admin dipendente da dealer non importato come organization, `2` assignment commerciali senza membership dealer prerequisita |
| MSN-AUTH-032 | Auth-org residual review policy slice | design | validato | Policy prudenziale fissata: `dealer.tipo=admin` senza identita' forte restano review-only, `mac` non viene promosso a organization implicita, assignment commerciali senza membership prerequisita restano esclusi dal gate |
| MSN-AUTH-033 | Auth-org dealer-admin and commercial-membership target decision | design | validato | Chiarito il target dei residui: i principal `dealer admin` escono dal lane dealer-scoped create-only; gli assignment commerciali restano subordinati a una membership minima esplicita dell'operatore sul dealer |
| MSN-AUTH-034 | Auth-org commercial membership minimum slice | implementazione | validato | `dealer_operatore_figura` puo' ora produrre una membership minima `dealer_operator_member`; nel dry-run reale i residui review scendono da `19` a `17` e risultano trattabili `2` membership commerciali + `2` assignment |
| MSN-AUTH-035 | Auth-org create-only second pass commercial lane | implementazione | validato | Secondo passaggio create-only eseguito sul dataset reale: committate `2` membership `dealer_operator_member` e `2` assignment; al rerun `ready_create = 0`, `manual_review = 17` |
| MSN-AUTH-036 | Auth-org dealer-admin residual lane baseline | audit | validato | Residuo finale consolidato: `16` principal `dealer.tipo=admin` review-only + `1` membership dealer_admin sul code `mac`; nessun altro `ready_create` residuo nel dataset reale |
| MSN-AUTH-037 | Auth-org dealer-admin target categories | design | validato | Residui admin categorizzati in principal tecnici/test, principal umani di supporto e principal corporate ambigui; lane automatico confermato chiuso e futuro seguito confinato alla review manuale |
| MSN-AUTH-038 | Auth-org dealer-admin manual reconciliation policy | design | validato | Esiti operativi fissati per il lane admin: `do_not_migrate_automatically`, `manual_link_only_if_real_neo_account_exists`, `manual_target_decision_required` |
| MSN-AUTH-039 | Auth-org case mac target decision | design | validato | `mac` fissato come principal amministrativo legacy corporate-ambiguous: nessuna promozione automatica a user o organization Neo, solo review/manual reconciliation o no-migration |
| MSN-AUTH-040 | Auth-org supporto manual link candidate | audit | validato | `supporto` confermato come miglior candidato umano del lane admin: possibile `manual_link_candidate` verso utente Neo reale con email `liberato.malvasi@hotmail.com`, ma senza auto-link |
| MSN-AUTH-041 | Auth-org dealer-admin human shortlist | audit | validato | Short-list minima consolidata del lane umano/admin: `supporto` come `manual_link_candidate`, `admin` e `rosy` come `manual_review_only`, `bo_*` esclusi dal lane umano |
| MSN-AUD-002 | Legacy auth-org import semantics audit | audit | validato | Audit esplorativo DB + codice CI3: `dealer` miscela principal interni e organization, `dealer_collaboratore` ospita seller autenticabili dealer-scoped, `commerciali` ospita operatori runtime con portafoglio dealer e policy dedicate |
| MSN-AUTH-042 | Auth-org import lane refinement | design | validato | Lane semantici di candidate classification fissati: `internal_platform_principal`, `dealer_organization`, `dealer_seller`, `dealer_operator`; il reconciliation layer futuro deve classificare prima per semantica e solo dopo per action/matching |
| MSN-AUTH-043 | Auth-org reconciliation lane-classification slice | implementazione | validato | Il dry-run e il report tecnico espongono ora `candidate_lane`, `candidate_lane_subtype` e `lane_breakdown`; il layer AUTH/ORG distingue esplicitamente internal principals, dealer organization, seller e operatori prima delle decisioni di matching/review |
| MSN-AUTH-044 | Auth-org unknown lane refinement slice | implementazione | validato | `dealer_user_assignment` ricondotto al lane semantico `dealer_operator`; il lane `unknown` resta solo fallback per record davvero non classificati |
| MSN-AUTH-045 | Auth-org internal principal subtype reporting slice | implementazione | validato | Il reconciliation report espone `lane_subtype_breakdown`, rendendo piu' leggibili i sottotipi del lane `internal_platform_principal` e degli operatori senza modificare il gate |
| MSN-AUTH-046 | Auth-org internal principal category refinement slice | implementazione | validato | Il reconciliation report espone `internal_principal_category_breakdown`, separando i residui `internal_platform_principal` in `human_plausible`, `technical_or_test` e `corporate` |
| MSN-AUTH-047 | Auth-org internal principal review-action refinement slice | implementazione | validato | Il reconciliation report espone `internal_principal_review_action_breakdown`, traducendo i principal interni residui in esiti operativi di review senza riaprire il gate |
| MSN-AUTH-048 | Auth-org internal principals review refinement | audit | validato | `supporto`, `admin` e `rosy` raffinati con evidenze legacy e handling proposal: `supporto` resta `manual_link_candidate`, `admin` e `rosy` restano `manual_review_only` |

## Dipendenze logiche
- `MSN-GOV-001` sblocca il metodo di lavoro comune.
- `MSN-GOV-003` rende obbligatoria la tracciabilita' legacy -> target per ogni bounded context con impatto dati.
- `MSN-DEP-001` fornisce il workspace tecnico reale su cui aprire i prossimi foundation module.
- `MSN-AUD-001` consolida e verifica i target model iniziali di `MSN-AUTH-001` e `MSN-ORG-001`.
- `MSN-AUTH-001` e `MSN-ORG-001` devono chiarire ruoli, permessi, assegnazioni e perimetro organizzativo prima di molti moduli business.
- `MSN-SHARED-001` traduce questi esiti in contratti condivisi per sidebar, capability e payload auth.
- `MSN-CAT-001` prepara la fondazione `fornitore -> compagnia -> prodotto`.
- `MSN-AUTH-002` e' il primo step implementativo sensato dopo il design shared.

## Cosa e' gia' chiarito
- Stack target: Laravel + Vue
- Direzione: backend-first / API-first / mobile-ready
- Metodo: bounded context progressivi, step piccoli, stato esplicito
- Legacy: baseline funzionale, non baseline architetturale
- Bootstrap reale del portale presente e verificato in locale
- Shell UI iniziale riallineata su base visuale Velzon
- Artefatto temporaneo di bootstrap rimosso dalla root del workspace
- Target model iniziale di identity chiarito come separazione tra account, membership, assignment e capability
- Target model iniziale di organization chiarito come separazione tra organization aggregate, membership, unit/scope e assignment
- Audit legacy iniziale conferma ingressi distinti `admin`, `dealer` e API JWT con principal non unificato
- Audit legacy iniziale conferma presenza di commerciali/account come figure operative e assignment specifici verso dealer
- Contratto target shared chiarito: capability backend-owned, `/api/v1/me` come payload canonico iniziale, sidebar derivata dal backend
- Primo slice shared implementato senza widening: `auth.context`, `ResolveAuthenticatedPortalContext`, `me.context`, navigation metadata stabili
- Persistence minima introdotta per membership organization, con seed bootstrap e contesto attivo reale nel workspace locale
- Regola del context raffinata: primary membership first, fallback prudente e contract read-only per memberships multiple
- Switching minimo del context attivo disponibile via API/sessione, con ownership validation e fallback sicuro
- Capability shared allineate al context attivo e navigation con scope backend-driven
- Audit catalogo iniziale consolidato: nel legacy il centro operativo e' `compagnia + garanzie + abilitazioni dealer`, non un catalog aggregate pulito
- Target model catalogo iniziale chiarito: separazione tra struttura catalogo, pricing profile e dealer enablement
- Primo slice tecnico catalogo introdotto nel nuovo portale con persistence minima e naming pulito
- Boundary pricing/enablement chiarito: pricing come configurazione economica, enablement come disponibilita' commerciale
- Primo slice tecnico di dealer enablement introdotto con relation `organization -> product`
- Governance permanente del filone import legacy -> Neo introdotta in documenti root e supporto tecnico shared minimale
- Primo pacchetto import-governance applicato a `Identity + Organization`, con schema target, mapping iniziale, strategia di import e gap dichiarati
- Disegno tecnico auth/org chiarito per legacy references: mapping table preferite su account e membership, `legacy_id` diretto solo per equivalenze semplici
- Persistence tecnica minima auth/org introdotta con mapping table polimorfica e seed bootstrap di riferimento
- Contratto minimo del comando auth/org chiarito: input, dry-run, output tecnico e regole di idempotenza
- Primo comando tecnico auth/org introdotto in modalita' dry-run con output tecnico e zero scritture
- Contratto del primo adapter legacy reale chiarito, con comando stabile e fallback controllato preservato
- Primo adapter legacy reale read-only introdotto con configurazione Laragon-friendly e contratto comando invariato
- Primo layer di candidate resolution chiarito sopra l'adapter auth/org, con type/status/reason espliciti
- Primo resolver read-only auth/org introdotto e agganciato al dry-run con output piu' ricco
- Primo layer di candidate matching chiarito con `match_status` e `match_reason` sopra i candidate risolti
- Primo matcher read-only auth/org introdotto e agganciato al dry-run con metriche di matching verso target Neo
- Prime euristiche di matching read-only chiarite per user, organization e membership oltre il bootstrap statico
- Prime euristiche concrete di matching introdotte nel dry-run auth/org con segnali normalizzati e metriche dedicate
- Edge case del matching read-only chiariti: ambiguous signals, weak signals e stop conditions
- Prima gestione concreta degli edge case introdotta nel dry-run auth/org con dataset dedicato e batch_result prudenziale
- Persistence minima reale AUTH/ORG consolidata con `parent_organization_id`, `dealer_operator_assignments` e `context.assignments`
- Foundation di import reconciliation auth/org consolidata con `legacy_entity_mappings`, snapshot read-only e matching anche per assignment
- Dry-run reale AUTH/ORG eseguito su `sql1483615_1` con esito `NO-GO` al commit: troppe ambiguita' su user e membership
- Correzione foundation AUTH/ORG applicata: i venditori dealer legacy sono ora esplicitamente trattati come account autenticabili con membership dealer-scoped, non come dettaglio secondario del dealer
- Signal hardening AUTH/ORG applicato: placeholder e pseudo-email filtrati meglio, pairing membership dealer-scoped piu' leggibile e dealer admin mantenuti come caso speciale da review
- Secondo dry-run reale AUTH/ORG eseguito dopo il signal hardening:
  - stesso volume record (`1621`)
  - `ready_create` saliti da `595` a `1604`
  - review scese da `1026` a `17`
  - review residue concentrate soprattutto su `dealer admin`
- Commit gate create-only AUTH/ORG ora implementato:
  - commit limitato ai candidati `ready_create`
  - esclusione esplicita dei `17` casi residue in review
  - nessun merge con target Neo esistenti
  - idempotenza e tracing tecnico via `legacy_entity_mappings`
- Primo commit controllato AUTH/ORG eseguito in locale sul dataset reale:
  - `committed_users = 654`
  - `committed_organizations = 272`
  - `committed_memberships = 425`
  - `committed_assignments = 1`
  - `mappings_created = 1351`
  - `synthetic_emails_assigned = 505`
  - `16` review residue escluse
- Follow-up post-create-only consolidato:
  - review residue rimaste tutte su `dealer admin` senza identita' forte;
  - al rerun non si osservano duplicazioni;
  - restano `3` candidati create-only non committati per dipendenze mancanti (`1` membership, `2` assignment).
- Dependency guard slice AUTH/ORG applicato dopo il create-only:
  - il dry-run reale non espone piu' falsi `ready_create` non soddisfabili dal gate;
  - i residui sono ora tutti classificati correttamente in review:
    - `16` `dealer_admin_requires_manual_identity_resolution`
    - `1` `membership_candidate_requires_resolved_organization_dependency`
    - `2` `assignment_candidate_requires_resolved_membership_dependency`
  - il commit `create-only` resta fermo a `1351` mapping tecnici gia' consolidati, senza nuove scritture in questo slice.
- Baseline residui AUTH/ORG consolidata:
  - i `16` dealer admin review sono tutti principal legacy `dealer.tipo=admin` senza email affidabile;
  - la membership residua punta al dealer code `mac`, che nel legacy emerge come principal admin e non come organization dealer autonoma importabile;
  - i `2` assignment residui puntano al commerciale `liberato.malvasi@hotmail.com` verso i dealer `Macsolution1980@` e `m.car`, ma manca una membership dealer-scoped coerente per promuoverli automaticamente.
- Residual review policy AUTH/ORG chiarita:
  - nessuna synthetic identity per chiudere i `dealer admin` review;
  - nessuna organization implicita per il principal `mac`;
  - nessun assignment senza membership dealer-scoped prerequisita.
- Target decision residui AUTH/ORG chiarita:
  - i `dealer admin` legacy non vengono reinterpretati come seller o membership dealer create-only;
  - il lane corretto futuro e' un principal amministrativo dedicato o una riconciliazione manuale esplicita;
  - gli assignment commerciali potranno essere trattati solo introducendo una membership minima intenzionale dell'operatore sul dealer target.
- Commercial membership minimum slice AUTH/ORG applicato:
  - `dealer_operatore_figura` puo' generare membership minime `dealer_operator_member`;
  - gli assignment relativi tornano trattabili solo sopra tale membership;
  - nel dry-run reale il quadro diventa:
    - `ready_create_candidates = 4`
    - `manual_review_candidates = 17`
    - review residue limitate a `16` dealer admin legacy e `1` caso `mac`.
- Secondo passaggio create-only AUTH/ORG sul lane commerciale eseguito:
  - `committed_memberships = 2`
  - `committed_assignments = 2`
  - `mappings_created = 4`
  - il rerun successivo torna a:
    - `ready_create_candidates = 0`
    - `manual_review_candidates = 17`
    - `blocked_candidates = 0`
- Baseline finale residui AUTH/ORG consolidata:
  - `16` review residue sono principal `dealer.tipo=admin` senza identita' forte;
  - `1` review residua e' una membership `dealer_admin` sul code `mac`;
  - il lane commerciale e' chiuso, quindi il prossimo seguito non richiede altro signal hardening o widening del gate.
- Dealer-admin reconciliation lane chiarito a categorie:
  - principal `bo_*` e simili trattati come backoffice/test o service principal legacy;
  - principal tipo `supporto` trattabili solo via review manuale verso account Neo esistenti;
  - principal `mac` trattato come corporate/admin ambiguous principal, non come organization implicita.
- Dealer-admin reconciliation policy fissata:
  - `bo_*` e principal tecnici analoghi: non migrare automaticamente;
  - principal umani/supporto: link manuale solo se esiste un account Neo reale gia' verificato;
  - principal corporate ambigui come `mac`: decisione target separata e obbligatoria.
- Caso `mac` chiarito:
  - record legacy `dealer.tipo=admin` con `username = mac` e label `Mac Solution Srl`;
  - esiste anche un `dealer_collaboratore` amministratore omonimo agganciato a `id_dealer = 78`;
  - in Neo non esiste oggi ne' `organization.code = mac` ne' una membership associata;
  - decisione corrente: trattarlo come principal amministrativo legacy ambiguo e non migrarlo automaticamente.
- Caso `supporto` chiarito:
  - record legacy `dealer.tipo=admin` con `username = supporto` e label `Liberato Malvasi`;
  - in Neo esiste un utente reale con email `liberato.malvasi@hotmail.com`;
  - la corrispondenza e' sufficiente per un `manual_link_candidate`, ma non per un auto-link.
- Short-list minima del lane umano/admin ora fissata:
  - `supporto` = `manual_link_candidate`;
  - `admin` = `manual_review_only`;
  - `rosy` = `manual_review_only`;
  - `bo_*` e analoghi restano fuori come principal tecnici/test;
  - `mac` resta fuori come caso corporate separato.
- Audit semantico legacy AUTH/ORG consolidato:
  - `dealer` non rappresenta un solo tipo di attore, ma sia principal interni (`tipo=admin`) sia organization dealer (`tipo=dealer`);
  - `dealer_collaboratore` e' la sorgente vera dei seller dealer-scoped con credenziali proprie;
  - `commerciali` e' un lane distinto di operatori autenticabili (`commerciale` / `account`) che entra nel portale dealer con portafoglio assegnato e policy runtime;
  - ACL e sidebar backoffice stratificano i principal interni di piattaforma in gruppi diversi (`superadmin`, `supporto_tecnico`, `backoffice_admin`, `backoffice_operatore`, `backoffice_finanziamenti`, `backoffice_rca`) e non vanno tradotti 1:1 in ruoli di dominio Neo.
- Lane refinement AUTH/ORG ora fissato:
  - `internal_platform_principal`
  - `dealer_organization`
  - `dealer_seller`
  - `dealer_operator`
- Conseguenza pratica:
  - il reconciliation layer non dovrebbe piu' leggere `dealer`, `dealer_collaboratore` o `commerciali` come scorciatoie dirette di target, ma come sorgenti da tradurre prima in un lane semantico stabile.
- Lane-classification slice ora implementato:
  - `candidate_lane` e `candidate_lane_subtype` sono disponibili nel dry-run AUTH/ORG;
  - il report tecnico espone `lane_breakdown`;
  - il lane `unknown` resta solo fallback per record realmente non classificati.
- Unknown-lane refinement slice ora implementato:
  - `dealer_user_assignment` viene ricondotto in modo esplicito al lane `dealer_operator`;
  - il bootstrap AUTH/ORG non lascia piu' record bridge noti in `unknown`;
  - il prossimo refinement sensato riguarda i residui `internal_platform_principal`, non i bridge legacy gia' compresi.
- Internal principal subtype reporting slice ora implementato:
  - il report tecnico espone `lane_subtype_breakdown`;
  - i residui `internal_platform_principal` possono essere letti per sottotipo semantico e non solo come lane aggregato;
  - i casi interni non ancora abbastanza precisi vengono esposti come `unclassified_internal_principal`;
  - il prossimo refinement sensato e' sui sottotipi residui reali, non sulla struttura del report.
- Internal principal category refinement slice ora implementato:
  - il report tecnico espone `internal_principal_category_breakdown`;
  - i residui admin/backoffice vengono letti subito come `human_plausible`, `technical_or_test` o `corporate`;
  - questo riduce l'opacita' del lane residuale senza riaprire il gate `create-only`.
- Internal principal review-action refinement slice ora implementato:
  - il report tecnico espone `internal_principal_review_action_breakdown`;
  - i principal interni residui vengono letti direttamente come `manual_link_candidate`, `manual_review_only`, `do_not_migrate_automatically` o `manual_target_decision_required`;
  - il prossimo passo sensato e' lavorare sui casi `manual_link_candidate` e `manual_review_only`, non cambiare il gate.
- Internal principals review refinement ora consolidato:
  - `supporto` resta l'unico caso con profilo da `manual_link_candidate`, grazie al forte allineamento col lane `supporto_tecnico`;
  - `admin` resta `manual_review_only`, perche' rappresenta un principal superadmin troppo generico per un link o import automatico;
  - `rosy` resta `manual_review_only`, perche' il ruolo appare umano e operativo ma non emerge ancora un segnale identitario forte verso un target Neo.
- Priorita' iniziale: foundation identity + organization, poi catalog foundation

## Cosa non e' ancora chiarito
- Target model concreto del primo `pricing profile` minimale
- Strategia operativa di convivenza dettagliata tra CI3 e nuovo workspace per singolo contesto

## Rischi da evitare
- Portare nel nuovo workspace naming, coupling o shortcut del legacy senza audit
- Aprire implementazioni UI o modulo senza foundation backend e contratti chiari
- Confondere analisi del legacy con approvazione del modello storico
- Allargare il perimetro a piu' moduli contemporaneamente troppo presto
- Frammentare la roadmap in micro-step concettuali che non corrispondono a veri deliverable

## Focus corrente consigliato
Aprire un lane AUTH/ORG separato di manual reconciliation / target decision per:
- i principal umani residui del lane `dealer admin`, partendo da `supporto`, `admin` e `rosy`
- il caso `mac` solo come traccia separata gia' classificata `manual_target_decision_required`
- e usare l'audit semantico legacy come base per affinare:
  - mapping degli internal platform principals;
  - distinzione tra seller dealer-scoped e operatori `commerciale/account`;
  - regole future di import/reconciliation non create-only.
- Il prossimo step sensato non e' un nuovo commit, ma il riallineamento del reconciliation layer a questi lane semantici.
- Questo riallineamento e' ora presente nel dry-run; il prossimo passo sensato e' decidere il trattamento operativo del caso `supporto` o continuare il refinement dei `manual_review_only` residui del lane `internal_platform_principal`.

## Roadmap iniziale realistica
1. Ridurre le ambiguita' reali del dataset auth/org con signal hardening e regole di normalizzazione
2. Chiarire il primo target model minimale del pricing profile
3. Estendere progressivamente scope, assignments e contratti shared
4. Consolidare i contratti catalogo lato API e admin foundation
5. Aprire il primo modulo business solo dopo fondazioni, mapping e strategie di import minime chiarite
