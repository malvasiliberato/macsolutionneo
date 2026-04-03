# Mac Solution Neo - Scoped Capability Resolution Slice

## Contesto
Il workspace Neo disponeva gia' di:
- context autenticato shared;
- persistence minima di organization membership;
- switching minimo del context attivo.

Serviva ora fare in modo che capability e navigation non fossero piu' solo statiche rispetto ai ruoli bootstrap, ma iniziassero a dipendere anche dal context attivo selezionato.

## Obiettivo del task
Implementare `MSN-SHARED-002` come slice shared capability/navigation context-aware.

## Decisioni adottate

### Capability resolution
- Le capability possono ora derivare da tre fonti:
  - ruoli tecnici bootstrap;
  - ruolo della membership attiva;
  - stato del context attivo.

### Navigation resolution
- La navigation mantiene filtro backend-driven per capability.
- Ogni item puo' dichiarare uno scope:
  - `global`
  - `contextual`
- Gli item contextual dipendono da organization attiva e capability coerenti.

## Modifiche applicate
- Esteso `CurrentUserCapabilities` per ricevere anche il `context`.
- Introdotti mapping config:
  - `membership_role_capability_map`
  - `context_capability_map`
- Esteso `BuildPortalNavigation` con:
  - `scope`
  - `context_label`
  - vincoli su context switching e organization attiva
- Allineati `MeController`, `SetActiveMembershipController` e `HandleInertiaRequests` al nuovo ordine di risoluzione:
  - prima context
  - poi capability
  - poi navigation

## Effetto concreto
- Un owner della membership attiva riceve capability diverse da un member.
- Il cambio del context attivo puo' cambiare il set di capability esposte da `/api/v1/me`.
- La shell UI puo' mostrare item di navigation con badge contestuale derivato dal backend.

## Cosa e' stato volutamente lasciato fuori
- ACL finale di dominio
- capability granulari per catalogo/pratiche/preventivi
- navigation organization completa
- UI dedicata di context switching

## Verifiche
- test API aggiornati per confermare:
  - capability owner vs member
  - persistenza dello switch
  - presenza di capability context-aware
- build frontend riuscita

## Risultato finale
`MSN-SHARED-002` puo' essere considerato validato:
- capability e navigation non sono piu' solo bootstrap-role driven;
- il context attivo entra davvero nella risoluzione shared del portale;
- il workspace e' ora pronto ad aprire l'audit `catalog foundation` senza allargare il perimetro business.

## Step successivo consigliato
Aprire `MSN-CAT-001` con output separato in:
- comportamento legacy osservato
- target model catalogo corretto
- strategia di transizione graduale
