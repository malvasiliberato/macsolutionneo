# Contesto
Mac Solution Neo dispone gia' di:
- persistence minima AUTH/ORG;
- `legacy_entity_mappings` per tracing tecnico;
- comando read-only `legacy:import:auth-org --dry-run`;
- candidate resolution, matching, confidence ed edge case iniziali.

Mancava pero' una foundation piu' chiara di reconciliation auth/org che separasse in modo leggibile:
- matching;
- mapping;
- dry-run;
- commit mode;
- reporting degli esiti.

# Obiettivo del task
Consolidare la foundation tecnica e documentale della reconciliation AUTH/ORG legacy -> Neo, senza aprire ancora import massivi o commit mode.

# Strutture tecniche introdotte o consolidate
- Riutilizzata e confermata come base tecnica comune:
  - `legacy_entity_mappings`
  - `legacy_key_hash`
- Nuovo service di reporting tecnico:
  - `AuthOrgLegacyReconciliationReportBuilder`
- Dry-run auth/org esteso:
  - supporta anche candidati `assignment`
  - restituisce `summary` separato da `reconciliation`
- Command console aggiornato:
  - stampa sia il riepilogo batch sia lo snapshot di reconciliation

# Regole di matching iniziali
- `user` -> `users`
  - email normalizzata
  - fallback a mapping tecnico
- `organization` -> `organizations`
  - code normalizzato
  - fallback a mapping tecnico
- `membership` -> `organization_memberships`
  - coppia `legacy_user_email + legacy_organization_code`
  - fallback a mapping tecnico
- `assignment` -> `dealer_operator_assignments`
  - tripletta `legacy_user_email + legacy_dealer_code + legacy_assignment_role_code`
  - fallback a mapping tecnico

# Strategia dry-run vs commit
- `dry-run`
  - legge sorgenti legacy
  - normalizza candidati
  - applica matching
  - produce summary e snapshot di reconciliation
  - non scrive in modo distruttivo sul dominio finale
- `commit mode`
  - volutamente non disponibile in questo step
  - rimandato a un macro-step successivo con gate di sicurezza esplicito

# Mapping legacy -> target
- `dealer` legacy admin:
  - candidato `user`
- `dealer` legacy commerciale/organizzativo:
  - candidato `organization`
- `dealer_user_assignment`:
  - puo' generare sia candidato `membership` sia candidato `assignment`
- `legacy_entity_mappings`:
  - resta il layer tecnico comune per idempotenza, tracing e audit del mapping

# Collisioni e ambiguita' note
- dealer legacy talvolta account, talvolta organization
- figure `commerciale/account` non ancora abbastanza pulite per regole finali
- assignment legacy a volte incompleti o con segnali deboli
- commit mode ancora da bloccare dietro soglie e review flow espliciti

# Cosa e' stato implementato davvero
- service separato di reconciliation reporting
- estensione del dry-run auth/org a candidati `assignment`
- matching keys esplicite nel report tecnico
- metriche su auto-match, importabile ad alta confidenza e review manuale
- adapter legacy reso piu' tollerante a colonne opzionali

# Cosa e' stato volutamente rimandato
- commit mode reale
- import massivo auth/org
- review UI o dashboard di riconciliazione
- sync bidirezionale
- organization domain completo

# Come questo step prepara l'import auth/org reale
Questo step rende finalmente governabile la fase pre-import:
- tracing tecnico stabile;
- matching esplicito per tutti gli oggetti foundation auth/org;
- reporting leggibile delle ambiguita';
- base idempotente riusabile prima di qualunque scrittura reale.
