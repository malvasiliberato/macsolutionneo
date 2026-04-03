# Identity Matching Edge Cases Slice

## Contesto
Gli edge case del matching read-only auth/org erano gia' chiariti a livello design. Restava da introdurre la prima gestione concreta nel matcher.

## Obiettivo del task
Implementare `MSN-AUTH-018` introducendo:
- gestione concreta dei segnali deboli;
- gestione concreta dei pair incompleti;
- escalation prudente del `batch_result`.

## Scelte implementative
- Introdotto `DefaultAuthOrgLegacyMatchingEdgeCases`
- Agganciato il matcher agli edge case read-only
- Aggiunto dataset controllato `bootstrap-auth-org-edge-cases`
- Il `batch_result` passa a `needs_reconciliation` anche con `ambiguous_match`

## Edge case concreti coperti
- organization con solo nome normalizzato
- membership con pair incompleta
- user senza segnale sufficiente

## Risultato finale
`MSN-AUTH-018` puo' considerarsi validato: il matcher read-only auth/org gestisce ora i primi edge case in modo prudente e verificabile, senza introdurre scritture o commit mode.

## Prossimo step consigliato
Aprire `MSN-AUTH-019` per chiarire un primo livello di confidence scoring read-only nel matching auth/org.
