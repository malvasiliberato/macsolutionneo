# Identity Candidate Resolution Design

## Contesto
Il comando auth/org dispone gia' di:
- dataset controllato
- adapter legacy reale read-only
- output tecnico stabile

Restava da chiarire il livello intermedio tra righe legacy grezze e futuri target Neo: la candidate resolution read-only.

## Obiettivo del task
Validare `MSN-AUTH-011` chiarendo il primo layer di resolution sopra l'adapter legacy, senza introdurre ancora scritture o mapping verso il dominio.

## Decisioni adottate
- La candidate resolution resta read-only.
- Non crea ancora `users`, `organizations` o `organization_memberships`.
- Produce tre elementi minimi espliciti:
  - `candidate_type`
  - `resolution_status`
  - `resolution_reason`

## Vocabolario minimo iniziale

### candidate_type
- `user`
- `organization`
- `membership`
- `unknown`

### resolution_status
- `mapped_candidate`
- `needs_review`
- `collision`
- `unresolved`

### resolution_reason
- motivo tecnico o semantico della classificazione
- utile per audit, debugging e prossima riconciliazione

## Perche' questo layer serve
- evita che il comando resti accoppiato a logica grezza o implicita
- permette di arricchire il dry-run senza aprire commit mode
- prepara un futuro matching piu' esplicito verso il target Neo

## Supporto introdotto
- `app/Application/Auth/LegacyImport/Contracts/AuthOrgLegacyCandidateResolver.php`

## Cosa resta volutamente fuori
- match contro target Neo reale
- scritture su mapping table
- commit mode
- import completo auth/org

## Risultato finale
`MSN-AUTH-011` puo' considerarsi validato: il workspace ha ora un contratto chiaro per il primo resolver read-only sopra l'adapter auth/org.

## Prossimo step consigliato
Aprire `MSN-AUTH-012` per implementare il primo resolver read-only e far emergere `candidate_type`, `resolution_status` e `resolution_reason` nel dry-run.
