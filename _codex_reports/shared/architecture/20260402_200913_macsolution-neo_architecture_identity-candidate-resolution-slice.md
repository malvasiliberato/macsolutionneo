# Identity Candidate Resolution Slice

## Contesto
Il design del layer di candidate resolution era gia' chiarito. Restava da introdurre una prima implementazione read-only agganciata al dry-run auth/org.

## Obiettivo del task
Implementare `MSN-AUTH-012` introducendo:
- un resolver read-only concreto;
- arricchimento dell'output del dry-run;
- nessuna scrittura su target Neo o mapping.

## Scelte implementative
- Introdotto `DefaultAuthOrgLegacyCandidateResolver`
- Agganciato il resolver a `AuthOrgLegacyDryRun`
- Esteso il summary con:
  - `needs_review`
  - `resolved_candidates`

## Effetto pratico
Il dry-run non si limita piu' a contare righe grezze, ma passa da una prima risoluzione semantica esplicita:
- `candidate_type`
- `resolution_status`
- `resolution_reason`

## Perimetro rispettato
- nessuna scrittura
- nessun commit mode
- nessun matching su target Neo reale
- nessun import completo

## Risultato finale
`MSN-AUTH-012` puo' considerarsi validato: il comando auth/org resta read-only ma ora espone una lettura piu' utile e governabile dei candidati legacy.

## Prossimo step consigliato
Aprire `MSN-AUTH-013` per chiarire il primo matching read-only tra candidate risolti e target Neo gia' esistenti.
