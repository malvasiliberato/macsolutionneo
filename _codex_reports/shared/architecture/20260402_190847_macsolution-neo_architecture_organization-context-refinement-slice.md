# Mac Solution Neo - Organization Context Refinement Slice

## Contesto
Nel workspace Neo era gia' presente una persistence minima per organization membership e un resolver del contesto autenticato. Serviva ora chiarire meglio la regola di selezione del contesto attivo e preparare il terreno a un futuro context switching, senza aprire ancora UI organization o moduli business.

## Obiettivo del task
Definire e implementare `MSN-ORG-002` come refinement leggero del context model organization.

## Decisioni adottate

### Regola di selezione del context attivo
- La membership primaria attiva ha precedenza.
- Se non esiste una membership primaria attiva, viene usata la prima membership attiva disponibile.
- Se non esistono membership attive, resta valido il fallback bootstrap-safe.

### Contract condiviso del context
Il contract `context` espone ora in modo piu' completo:
- `active_organization`
- `active_membership`
- `active_scope`
- `available_memberships`
- `available_organizations`
- `context_switching`

### Context switching solo dichiarativo
- In questa fase `context_switching` e' solo read-only.
- Serve a dichiarare la strategia corrente e se il caso multi-membership esiste.
- Non introduce ancora endpoint, session state o UI di switch.

## Modifiche applicate
- Raffinato `ResolveAuthenticatedPortalContext`.
- Aggiunta risoluzione esplicita `primary_membership_first`.
- Esteso il payload `context` con metadata sulle membership disponibili e sullo switching.
- Aggiornati i test API per coprire il caso multi-membership.

## Cosa e' stato volutamente lasciato fuori
- endpoint di cambio contesto
- persistenza di un context attivo scelto dall'utente
- UI di context switching
- scope organization interni
- capability realmente scoped su membership multiple

## Motivazione
- Il bootstrap aveva gia' abbastanza struttura per chiarire la regola del contesto senza fare un salto prematuro verso flussi completi.
- La disponibilita' del contract read-only consente ai prossimi step di lavorare su basi stabili e verificabili.

## Risultato finale
`MSN-ORG-002` puo' essere considerato validato:
- la regola del contesto attivo e' esplicita;
- il caso multi-membership e' riconosciuto dal contract shared;
- il prossimo passo corretto e' uno slice implementativo minimo di context switching.

## Step successivo consigliato
Aprire `MSN-ORG-003` con focus su:
- endpoint o azione minima per selezionare il contesto attivo
- persistenza prudente della scelta
- riallineamento di `/api/v1/me` e Inertia sul context attivo selezionato
