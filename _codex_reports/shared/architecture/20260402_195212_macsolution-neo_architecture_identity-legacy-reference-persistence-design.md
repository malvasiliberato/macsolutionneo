# Identity Legacy Reference Persistence Design

## Contesto
Il workspace ha gia' chiarito:
- target model `Identity + Organization`
- audit legacy auth/org
- primo pacchetto `schema target + mapping + import strategy + gap`

Restava da decidere come persistire in modo prudente e governabile le legacy references per rendere possibili import e reimport futuri senza sporcare il dominio Neo.

## Obiettivo del task
Validare `MSN-AUTH-005` chiarendo dove usare:
- `legacy_id` semplice
- campi legacy multipli
- mapping table tecnica dedicata
- tracking tecnico esterno al dominio

con focus su:
- `users`
- `organizations`
- `organization_memberships`

## Problema da risolvere
- Nel legacy i principal auth/org non sono unificati.
- La provenienza storica dei dati puo' essere multi-sorgente e non sempre uno-a-uno.
- Un tracking troppo vicino al dominio rischia di irrigidire male il modello Neo.
- Un tracking troppo esterno e non strutturato rischia di rendere fragile l'idempotenza.

## Decisioni adottate

### 1. `users`
- Decisione raccomandata: mapping table tecnica dedicata.
- Motivo:
  - la provenienza legacy puo' essere multipla;
  - i principal legacy non coincidono sempre con un unico soggetto di dominio;
  - serve supportare riconciliazione e reimport senza moltiplicare campi `legacy_*` su `users`.

### 2. `organizations`
- Decisione raccomandata:
  - `legacy_id` diretto solo quando il dealer legacy coincide in modo semplice e stabile con una organization Neo;
  - mapping table dedicata quando la provenienza e' ambigua, multi-sorgente o destinata a merge.

### 3. `organization_memberships`
- Decisione raccomandata: mapping table tecnica dedicata quasi sempre.
- Motivo:
  - la membership spesso non corrisponde a un singolo record legacy;
  - puo' derivare da join, regole di riconciliazione o inferenze di appartenenza;
  - serve tenere separato il tracciamento tecnico dal linguaggio di dominio.

## Standard tecnico minimo raccomandato
Una mapping table tecnica auth/org dovrebbe poter registrare almeno:
- `source_system`
- `legacy_table`
- `legacy_key`
- `target_type`
- `target_id`
- `mapping_status`
- `last_imported_at`
- eventuale `checksum` o fingerprint tecnico

## Regola pratica per la scelta
- Usare `legacy_id` nel dominio solo se:
  - il match e' uno-a-uno;
  - la sorgente e' unica;
  - il significato e' stabile anche nel linguaggio di business.
- Usare mapping table tecnica se:
  - la provenienza e' multi-sorgente;
  - la chiave e' composta;
  - il match richiede riconciliazione;
  - serve reimport idempotente;
  - il riferimento legacy e' puramente tecnico.

## Cosa e' stato lasciato volutamente fuori
- shape definitiva delle tabelle di mapping
- migration concreta
- comandi artisan di import
- implementazione del primo import auth/org

## Risultato finale
`MSN-AUTH-005` puo' considerarsi validato:
- `users`: mapping table tecnica preferita
- `organizations`: `legacy_id` diretto solo nei casi semplici, altrimenti mapping table
- `organization_memberships`: mapping table tecnica preferita

## Perche' questo prepara bene il prossimo step
- sblocca una persistence tecnica minima senza confondere import tracking e dominio
- prepara un primo slice implementativo ristretto e verificabile
- mantiene il workspace coerente con la governance `legacy -> Neo`

## Prossimo step consigliato
Aprire `MSN-AUTH-006` per introdurre una persistence tecnica minima delle legacy references su auth/org, senza implementare ancora gli import completi.
