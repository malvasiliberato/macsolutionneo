# Identity Matching Heuristics Slice

## Contesto
Le euristiche di matching erano gia' chiarite a livello design. Restava da introdurre le prime regole concrete nel matcher read-only auth/org.

## Obiettivo del task
Implementare `MSN-AUTH-016` introducendo le prime euristiche concrete:
- `user` tramite email normalizzata
- `organization` tramite code normalizzato
- `membership` tramite coppia `user_email + organization_code`

## Scelte implementative
- Introdotto `DefaultAuthOrgLegacyMatchingHeuristics`
- Agganciato il matcher a segnali concreti ma prudenziali
- Esteso il dry-run con:
  - `heuristic_matches`
  - `mapping_matches`

## Risultato pratico
Il matching non dipende piu' solo dal bootstrap statico, ma inizia a usare segnali normalizzati leggibili e riusabili.

## Perimetro rispettato
- nessuna scrittura
- nessun commit mode
- nessun import completo
- nessun merge automatico

## Prossimo step consigliato
Aprire `MSN-AUTH-017` per chiarire collisioni, segnali deboli e casi ambigui del matching read-only auth/org.
