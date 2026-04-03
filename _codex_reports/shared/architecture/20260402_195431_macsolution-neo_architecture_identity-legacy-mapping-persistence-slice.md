# Identity Legacy Mapping Persistence Slice

## Contesto
Il workspace aveva gia' chiarito:
- schema target auth/org
- mapping legacy -> target
- strategia di import per fasi
- regole per scegliere tra `legacy_id` diretto e mapping table tecnica

Restava da introdurre una persistence tecnica minima e reale per sostenere import e reimport futuri senza sporcare il dominio applicativo.

## Obiettivo del task
Implementare `MSN-AUTH-006` con una base tecnica minima per le legacy references di `Identity + Organization`, mantenendo il tracking separato dal dominio business.

## Decisione implementata
- Introdotta tabella tecnica `legacy_entity_mappings`
- Introdotto model `LegacyEntityMapping`
- Introdotte relazioni polimorfiche tecniche su:
  - `User`
  - `Organization`
  - `OrganizationMembership`

## Struttura della persistence
La tabella tecnica registra almeno:
- `source_system`
- `legacy_table`
- `legacy_id`
- `legacy_key`
- `legacy_key_hash`
- `target_type`
- `target_id`
- `mapping_status`
- `checksum`
- `last_imported_at`

## Motivazione delle scelte
- `legacy_key_hash` permette idempotenza su chiavi composte o multi-attributo.
- `target_type + target_id` permette di legare lo stesso meccanismo a piu' aggregate senza introdurre tabelle separate premature.
- Le relazioni polimorfiche restano tecniche e non ridefiniscono il linguaggio di dominio.
- La scelta evita di aggiungere subito molti campi `legacy_*` a `users`, `organizations` e `organization_memberships`.

## Seed tecnico minimale introdotto
Il bootstrap locale ora registra anche mapping tecnici di riferimento per:
- account bootstrap
- organization bootstrap
- membership bootstrap

Questo non implementa un import legacy reale, ma dimostra che la persistence tecnica e' funzionante e riutilizzabile.

## Cosa e' stato volutamente rimandato
- comandi artisan di import
- dry-run reale
- loader dal DB legacy
- policy di riconciliazione avanzata
- import completo auth/org

## Risultato finale
`MSN-AUTH-006` puo' considerarsi validato:
- la persistence tecnica minima esiste davvero nel workspace;
- supporta mapping multi-sorgente e idempotenza;
- resta separata dal dominio applicativo;
- prepara il primo contratto operativo di import auth/org.

## Prossimo step consigliato
Aprire `MSN-AUTH-007` per definire il primo contratto minimo di import auth/org:
- input legacy attesi
- dry-run
- output tecnico
- regole di idempotenza e riconciliazione
