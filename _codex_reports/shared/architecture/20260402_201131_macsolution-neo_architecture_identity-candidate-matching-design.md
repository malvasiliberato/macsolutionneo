# Identity Candidate Matching Design

## Contesto
Il workspace dispone gia' di:
- adapter legacy read-only
- candidate resolution read-only
- dry-run auth/org con output piu' ricco

Restava da chiarire il livello successivo: il matching read-only tra candidate legacy risolti e target Neo gia' esistenti.

## Obiettivo del task
Validare `MSN-AUTH-013` definendo il primo layer di matching read-only senza introdurre ancora scritture o merge.

## Decisioni adottate
- Il matching resta separato dalla candidate resolution.
- Il matching non crea ancora `users`, `organizations` o `organization_memberships`.
- Il matching produce almeno:
  - `match_status`
  - `match_reason`

## Vocabolario minimo iniziale

### match_status
- `matched_existing_target`
- `no_existing_match`
- `ambiguous_match`
- `not_applicable`

### match_reason
- spiegazione tecnica sintetica del perche' del match o del mancato match

## Regole di progetto
- `user` confrontato con `users`
- `organization` confrontata con `organizations`
- `membership` confrontata con `organization_memberships`
- nessuna scrittura
- nessun commit mode
- nessun merge automatico

## Supporto introdotto
- `app/Application/Auth/LegacyImport/Contracts/AuthOrgLegacyCandidateMatcher.php`

## Perche' questo layer serve
- evita di saltare da candidate resolution a import
- rende esplicito se il target Neo esiste gia'
- prepara un futuro matching implementativo prudente e verificabile

## Prossimo step consigliato
Aprire `MSN-AUTH-014` per implementare il primo matcher read-only auth/org verso target Neo esistenti.
