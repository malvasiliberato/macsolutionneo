# Mac Solution Neo - Organization Context Switching Slice

## Contesto
Il workspace Neo disponeva gia' di:
- persistence minima per organization membership;
- regola esplicita `primary membership first`;
- contract `context` condiviso tra `/api/v1/me` e Inertia.

Mancava ancora un modo minimale e governabile per selezionare un contesto attivo diverso dalla primary membership, senza aprire ancora una UI organization completa.

## Obiettivo del task
Implementare `MSN-ORG-003` come slice minimo di context switching.

## Decisioni adottate

### Persistenza della selezione
- La selezione del context attivo viene persistita in sessione autenticata.
- Questa scelta e' coerente con il bootstrap web attuale e con l'uso stateful di Sanctum nel contesto Laragon/web portal.

### Ownership validation
- Una membership puo' essere selezionata solo se:
  - appartiene all'utente autenticato;
  - e' attiva.

### Fallback sicuro
- Se non esiste selezione in sessione, o la selezione non e' piu' valida, il resolver torna a `primary membership first`.

## Modifiche applicate
- Aggiunto endpoint API:
  - `POST /api/v1/context/active-membership`
- Aggiunto controller:
  - `SetActiveMembershipController`
- Esteso `ResolveAuthenticatedPortalContext` per:
  - leggere la membership selezionata dalla sessione;
  - preferire la selezione valida rispetto alla primary membership;
  - esporre metadata sullo stato dello switching.

## Contract aggiornato
Il blocco `context.context_switching` espone ora almeno:
- `enabled`
- `strategy`
- `can_switch`
- `selected_membership_id`

Strategie possibili in questa fase:
- `primary_membership_first`
- `session_selected_membership`
- `bootstrap_fallback`

## Cosa e' stato volutamente lasciato fuori
- UI per cambiare organization context
- persistenza permanente cross-device della selezione
- audit trail del cambio contesto
- capability realmente scoped per organization attiva

## Verifiche
- test di selezione esplicita della membership attiva
- test di blocco su membership non posseduta
- suite completa verde

## Risultato finale
`MSN-ORG-003` puo' essere considerato validato:
- esiste uno switching minimo del context attivo;
- il contract shared resta coerente;
- il prossimo passo corretto e' rendere capability e navigation sensibili al context attivo selezionato.

## Step successivo consigliato
Aprire `MSN-SHARED-002` con focus su:
- capability scoped per context attivo
- navigation derivata anche dal context oltre che dalle capability
- allineamento progressivo di `/api/v1/me` e Inertia su questa risoluzione shared
