# Identity Legacy Dataset Adapter Slice

## Contesto
Il comando `legacy:import:auth-org --dry-run` era gia' disponibile con dataset controllato. Restava da introdurre un primo adapter legacy reale read-only senza modificare il contratto del comando.

## Obiettivo del task
Implementare `MSN-AUTH-010` introducendo:
- un adapter legacy reale read-only;
- configurazione minima dedicata;
- fallback invariato sul dataset controllato.

## Scelte implementative
- Introdotto `MySqlAuthOrgLegacyDatasetAdapter`
- Introdotto `config/legacy_import.php`
- Aggiunta connessione `legacy_mysql` in `config/database.php`
- Mantenuto `bootstrap-auth-org` come fallback
- Mantenuto `legacy:import:auth-org` come entrypoint stabile

## Comportamento dell'adapter
- supporta dataset `legacy-ci3-auth-org`
- legge in modalita' read-only da:
  - tabella dealer legacy
  - tabella membership legacy configurata
- normalizza le righe in shape minima:
  - `legacy_table`
  - `candidate`
  - `status`

## Sicurezza e perimetro
- nessuna scrittura su target Neo
- nessuna scrittura su `legacy_entity_mappings`
- nessun commit mode
- dataset controllato ancora disponibile in fallback

## Verifica eseguita
- test del comando con dataset controllato ancora verde
- test del comando con adapter reale read-only su tabelle legacy sintetiche

## Risultato finale
`MSN-AUTH-010` puo' considerarsi validato: il comando auth/org puo' ora funzionare sia con dataset controllato sia con un primo adapter legacy reale read-only, senza rompere il contratto gia' validato.

## Prossimo step consigliato
Aprire `MSN-AUTH-011` per chiarire il primo mapping reale delle righe lette dall'adapter verso candidate resolution piu' ricca, mantenendo ancora il perimetro read-only.
