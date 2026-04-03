# Identity Candidate Matching Slice

## Contesto
Il layer di matching read-only era gia' stato chiarito a livello design. Restava da introdurre una prima implementazione concreta sopra i candidate auth/org risolti.

## Obiettivo del task
Implementare `MSN-AUTH-014` introducendo:
- un matcher read-only concreto;
- metriche di matching nel dry-run;
- nessuna scrittura su target o mapping.

## Scelte implementative
- Introdotto `DefaultAuthOrgLegacyCandidateMatcher`
- Agganciato il matcher a `AuthOrgLegacyDryRun`
- Esteso il summary con:
  - `matched_existing_targets`
  - `unmatched_candidates`
  - `ambiguous_matches`

## Perimetro rispettato
- nessuna scrittura
- nessun commit mode
- nessun merge automatico
- matching solo read-only verso target Neo esistenti

## Risultato finale
`MSN-AUTH-014` puo' considerarsi validato: il dry-run auth/org ora espone anche un primo livello di matching verso target Neo esistenti, sempre in modalita' read-only.

## Prossimo step consigliato
Aprire `MSN-AUTH-015` per chiarire le prime euristiche di matching oltre il bootstrap statico.
