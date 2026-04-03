# Contesto
Mac Solution Neo dispone gia' di una foundation reale per AUTH/ORG import reconciliation:
- `legacy_entity_mappings`
- dry-run read-only
- matching esplicito per `user`, `organization`, `membership`, `assignment`
- reporting separato
- nessun commit mode prematuro

Il passo corretto ora era validare questa base su un dataset CI3 reale.

# Obiettivo del task
Eseguire un dry-run controllato e read-only sul dataset legacy reale disponibile, classificare gli esiti e misurare la qualita' del matching prima di qualunque decisione su commit/no-commit.

# Dataset e sorgenti legacy usate
- Database legacy reale usato: `sql1483615_1`
- Tabelle lette:
  - `dealer`
  - `dealer_collaboratore`
  - `commerciali`
  - `dealer_operatore_figura`

# Comandi o modalita' di esecuzione
- Dry-run operativo rieseguibile:
```powershell
$env:LEGACY_IMPORT_DB_CONNECTION='legacy_mysql'
$env:LEGACY_IMPORT_DB_HOST='localhost'
$env:LEGACY_IMPORT_DB_PORT='3306'
$env:LEGACY_IMPORT_DB_DATABASE='sql1483615_1'
$env:LEGACY_IMPORT_DB_USERNAME='root'
$env:LEGACY_IMPORT_DB_PASSWORD=''
php artisan legacy:import:auth-org --dry-run --dataset=legacy-ci3-auth-org --batch=5000
```
- In ambiente Laragon e' stato aggiunto un fallback CLI del dataset adapter, perche' la connessione PDO legacy locale non era configurata con credenziali effettivamente utilizzabili.

# Metriche aggregate
- `records_read = 1621`
- `user_candidates = 681`
- `organization_candidates = 274`
- `membership_candidates = 664`
- `assignment_candidates = 2`
- `matched_existing_targets = 0`
- `unmatched_candidates = 595`
- `ambiguous_matches = 1026`
- `high_confidence_matches = 0`
- `medium_confidence_matches = 0`
- `low_confidence_matches = 1026`
- `no_confidence_matches = 595`
- `batch_result = needs_reconciliation`

# Esiti per entita'
- `user`
  - `ready_create = 160`
  - `needs_review = 521`
  - pattern dominante: `user_candidate_has_insufficient_signals`
- `organization`
  - `ready_create = 274`
  - `needs_review = 0`
  - pattern dominante: `no_existing_organization_match_found`
- `membership`
  - `ready_create = 159`
  - `needs_review = 505`
  - pattern dominante: `membership_candidate_has_incomplete_pair_signal`
- `assignment`
  - `ready_create = 2`
  - `needs_review = 0`

# Classificazione finale del matching
- Match affidabili:
  - nessun `ready_link` verso target Neo gia' esistenti
- Review manuale:
  - 1026 casi ambigui o con segnali insufficienti
- Non importabili / bloccanti tecnici:
  - nessun `blocked` tecnico puro nel motore attuale
  - ma commit globale comunque `NO-GO` per qualita' insufficiente dei segnali auth/org

# Principali ambiguita' emerse
- 16 dealer `admin` senza email valida, quindi non riconciliabili come account in modo affidabile
- 302 `dealer_collaboratore` senza email valida
- 2 username dealer duplicate:
  - `giuseppe`
  - `Direzionalemac`
- 10 email duplicate tra i collaboratori
- il placeholder `-` compare 204 volte come pseudo-email nei collaboratori
- membership spesso prive di chiave utente affidabile per via di email mancanti o sporche

# Micro-fix applicati
- normalizzazione prudente delle email legacy sporche / placeholder
- fallback CLI read-only del dataset adapter per Laragon locale
- reporting piu' leggibile con breakdown per entita' e classificazione `ready_create / needs_review`

# Valutazione complessiva della readiness al commit futuro
- Stato attuale: `NO-GO`
- Motivazione:
  - zero auto-match affidabili verso target Neo
  - troppi casi review su `user` e `membership`
  - chiavi identitarie legacy non abbastanza stabili per un commit prudente

# Raccomandazione finale
`NO-GO` al commit controllato in questo stato.

Prima del commit serve un nuovo macro-step di signal hardening auth/org per:
- normalizzare e filtrare meglio i segnali identity;
- trattare dealer `admin` come caso speciale;
- definire regole prudenziali su email placeholder, duplicati e membership incomplete.
