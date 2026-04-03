# Mac Solution Neo - Identity Org Shared Foundation Slice

## Contesto
Il workspace Neo aveva gia' chiarito a livello design:
- target model iniziale di identity;
- target model iniziale di organization;
- governance shared di capability, `/api/v1/me` e sidebar backend-driven.

Serviva ora un primo slice implementativo stretto, senza aprire moduli business e senza introdurre ancora persistence organization completa.

## Obiettivo del task
Implementare `MSN-AUTH-002` come primo slice tecnico condiviso auth/org, preparando il bootstrap per:
- contesto autenticato condiviso;
- payload `me` piu' evolutivo;
- navigation metadata piu' stabili;
- crescita futura verso membership e scope.

## Modifiche applicate

### Resolver unico del contesto autenticato
- Introdotto `App\Support\Auth\ResolveAuthenticatedPortalContext`.
- In questa fase restituisce un contesto bootstrap-safe e dichiaratamente placeholder:
  - `is_bootstrap = true`
  - `active_organization = null`
  - `active_membership = null`
  - `active_scope = bootstrap`
  - `assignments = []`

### Contratto `/api/v1/me` evoluto
- `CurrentUserResource` ora espone anche `context`.
- `MeController` usa sia `CurrentUserCapabilities` sia `ResolveAuthenticatedPortalContext`.
- Il payload API resta compatibile ma diventa pronto a supportare organization context e scope futuri.

### Condivisione Inertia allineata al contract API
- `HandleInertiaRequests` ora condivide anche `auth.context`.
- La navigation viene costruita dal backend usando sia capability sia context.
- Questo allinea meglio frontend web e API sullo stesso concetto di contesto autenticato.

### Navigation metadata piu' stabili
- `config/portal.php` usa ora item con:
  - `key`
  - `required_capabilities`
  - `state`
- `BuildPortalNavigation` supporta metadata piu' evolutivi e resta retrocompatibile sul filtraggio.
- La sidebar Vue usa `item.key` e mostra il label del contesto bootstrap attivo in topbar.

## Cosa e' stato volutamente lasciato fuori
- tabelle definitive organization
- membership persistence reale
- scope reali per dealer/commerciali/account
- capability contestuali di dominio
- sidebar modulo per modulo

## Motivazione delle scelte
- Il bootstrap aveva gia' punti tecnici buoni da evolvere: `CurrentUserCapabilities`, `BuildPortalNavigation`, `HandleInertiaRequests`, `/api/v1/me`.
- Invece di introdurre subito tabelle o modelli pesanti, questo slice crea una superficie condivisa stabile da far crescere.
- Il contesto placeholder esplicito e' piu' governabile di assenze implicite o future aggiunte ad hoc.

## Verifica
- Aggiornato il test API `MeTest` per verificare:
  - capability presenti
  - `context.is_bootstrap`
  - `context.active_scope.code = bootstrap`

## Risultato finale
`MSN-AUTH-002` puo' essere considerato validato:
- il bootstrap Neo ha ora un primo contratto shared auth/org implementato;
- il frontend e l'API condividono una semantica piu' stabile del contesto autenticato;
- il prossimo passo corretto e' introdurre persistence minima di membership, non aprire ancora un modulo business.

## Step successivo consigliato
Aprire `MSN-AUTH-003` con focus su:
- persistence minima di organization membership
- collegamento prudente account -> membership
- esposizione del contesto attivo reale nel contract shared senza widening verso catalogo o pratiche
