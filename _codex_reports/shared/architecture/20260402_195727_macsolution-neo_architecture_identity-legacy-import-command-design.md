# Identity Legacy Import Command Design

## Contesto
Il workspace dispone ora di:
- target model auth/org chiarito;
- mapping e strategia di import iniziale;
- persistence tecnica minima tramite `legacy_entity_mappings`.

Il passo successivo prima di implementare un import vero e' definire il contratto minimo del primo comando tecnico auth/org.

## Obiettivo del task
Validare `MSN-AUTH-007` definendo:
- naming del comando;
- input minimi attesi;
- comportamento `dry-run`;
- output tecnico;
- regole minime di idempotenza e riconciliazione.

## Decisioni adottate

### Naming raccomandato
- `legacy:import:auth-org`
- dry-run tramite opzione `--dry-run`

### Input minimi attesi
- `source-system`
- `legacy-table` oppure dataset dichiarato
- `--batch` opzionale
- modalita' `--dry-run` oppure commit reale in step futuri

### Output tecnico minimo
Il comando dovrebbe produrre almeno:
- record letti
- candidati `users`
- candidati `organizations`
- membership candidate
- mapping creati
- mapping aggiornati
- collisioni o record non riconciliati
- esito finale del batch

### Regole minime di idempotenza
- stessa `legacy_key_hash` + stesso target: nessuna duplicazione
- stessa `legacy_key_hash` + target coerente: aggiornamento controllato
- stessa `legacy_key_hash` + target incompatibile: collisione esplicita da segnalare

### Regole minime di dry-run
- nessuna scrittura su aggregate target
- nessuna scrittura su `legacy_entity_mappings`
- produzione di output tecnico sufficiente per audit e riconciliazione

## Cosa e' volutamente fuori da questo step
- loader completo dal DB legacy
- import completo utenti/dealer
- assegnazione finale di ruoli scoped
- sincronizzazione bidirezionale
- orchestrazione multi-bounded-context

## Risultato finale
`MSN-AUTH-007` puo' considerarsi validato: il workspace ha ora un contratto chiaro per il primo comando auth/org, senza anticipare un import completo o non governato.

## Prossimo step consigliato
Aprire `MSN-AUTH-008` per introdurre un primo comando tecnico in modalita' `dry-run`, usando dataset controllato o input espliciti e producendo solo output tecnico verificabile.
