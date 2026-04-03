# Identity Matching Edge Cases Design

## Contesto
Il matcher read-only auth/org usa gia' segnali concreti. Restava da chiarire come trattare i casi non lineari senza forzare match fragili o prematuri.

## Obiettivo del task
Validare `MSN-AUTH-017` definendo:
- segnali ambigui
- segnali deboli
- stop conditions

per il matching read-only auth/org.

## Decisioni adottate

### Segnali ambigui
- piu' target Neo compatibili con lo stesso segnale forte
- segnali forti ma confliggenti tra loro
- mapping tecnico esistente in conflitto con il match euristico

### Segnali deboli
- solo nome normalizzato
- ruolo legacy da solo
- testo descrittivo non stabile
- join tecniche non ancora confermate semanticamente

### Stop conditions
- piu' match forti incompatibili
- target inattivo o semanticamente incompatibile
- assenza di segnali sufficienti
- conflitto tra mapping tecnico e candidato risolto

## Regola di prudenza
Gli edge case forti devono produrre `ambiguous_match` o `needs_review`, non `matched_existing_target`.

## Supporto introdotto
- `app/Application/Auth/LegacyImport/Contracts/AuthOrgLegacyMatchingEdgeCases.php`

## Risultato finale
`MSN-AUTH-017` puo' considerarsi validato: il workspace ha ora una base chiara per implementare il trattamento prudente degli edge case nel matcher read-only auth/org.

## Prossimo step consigliato
Aprire `MSN-AUTH-018` per introdurre la prima gestione concreta degli edge case nel matcher read-only auth/org.
