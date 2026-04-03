# Identity Legacy Dataset Adapter Design

## Contesto
Il comando `legacy:import:auth-org --dry-run` esiste gia' ed e' validato con dataset controllato. Restava da chiarire come collegare in futuro una sorgente legacy reale senza rompere il contratto gia' stabilizzato.

## Obiettivo del task
Validare `MSN-AUTH-009` definendo il primo adapter legacy reale per auth/org:
- read-only
- compatibile con il comando esistente
- sostituibile al dataset controllato senza cambiare l'output tecnico

## Decisioni adottate
- Il comando `legacy:import:auth-org` resta il punto di ingresso stabile.
- Il dataset controllato resta fallback di sicurezza.
- L'adapter legacy reale deve essere introdotto dietro un contratto dedicato, non con query sparse nel comando.

## Contratto raccomandato
- `supports(dataset)`
  - permette di distinguere dataset controllato e adapter reale
- `fetch(sourceSystem, legacyTable, batch)`
  - restituisce righe normalizzate nel formato atteso dal dry-run

## Shape minima delle righe
L'adapter deve restituire record gia' normalizzati almeno con:
- `legacy_table`
- `candidate`
- `status`

Questa shape resta volutamente minima per preservare il contratto attuale del comando.

## Regole di progetto
- read-only only nel primo adapter reale
- nessuna scrittura su target Neo
- nessuna scrittura su `legacy_entity_mappings`
- nessun commit mode
- nessuna logica CI3 distribuita direttamente nel command handler

## Cosa e' stato introdotto nel workspace
- Contratto tecnico:
  - `app/Application/Auth/LegacyImport/Contracts/AuthOrgLegacyDatasetAdapter.php`

## Perche' questa scelta e' prudente
- consente di collegare il legacy per gradi
- preserva il fallback controllato
- evita che il comando si accoppi subito a dettagli infrastrutturali del CI3
- mantiene testabile il comportamento read-only

## Prossimo step consigliato
Aprire `MSN-AUTH-010` per implementare un primo adapter legacy reale read-only, mantenendo invariato il contratto del comando e il fallback `bootstrap-auth-org`.
