# Mac Solution Neo - Membership Persistence Bootstrap Slice

## Contesto
Il workspace Neo disponeva gia' di:
- bootstrap reale Laravel + Vue;
- design validato di identity, organization e capability shared;
- primo slice shared su `context` autenticato e `/api/v1/me`.

Mancava ancora una persistence minima reale per collegare account e context organization in modo verificabile, senza introdurre moduli business o organization model definitivo.

## Obiettivo del task
Implementare `MSN-AUTH-003` come slice minimo di persistence per membership organization, con perimetro stretto su foundation auth/org.

## Decisioni adottate

### 1. Introdurre solo due entita' minime
- `organizations`
- `organization_memberships`

Non sono state introdotte:
- organization unit
- assignment di dominio
- permessi granulari
- tassonomie dealer/commerciali/account definitive

### 2. Treat organization come supporto foundation, non come dominio completo
- `organizations` serve qui come contenitore minimo per il context autenticato.
- Non rappresenta ancora il modello organization completo del dominio Mac Solution Neo.

### 3. Membership come relazione esplicita account -> organization
- `organization_memberships` collega `user` e `organization`.
- La membership include solo metadati minimi:
  - `role_code`
  - `status`
  - `is_primary`
  - `joined_at`

## Modifiche applicate
- Aggiunti i model:
  - `App\Models\Organization`
  - `App\Models\OrganizationMembership`
- Esteso `User` con relazione `organizationMemberships`.
- Aggiunta migration:
  - `organizations`
  - `organization_memberships`
- Aggiornato `ResolveAuthenticatedPortalContext` per risolvere il contesto a partire da membership attive.
- Aggiornato `DatabaseSeeder` con:
  - organization bootstrap `macsolution-neo`
  - membership primaria del bootstrap admin

## Effetto sul contract shared
Il contract `context` non e' piu' solo placeholder globale.
Ora puo' esporre in modo reale:
- `active_organization`
- `active_membership`
- `active_scope`
- `available_organizations`

In assenza di membership attive, il fallback bootstrap-safe resta disponibile.

## Cosa e' stato volutamente lasciato fuori
- cambio manuale del contesto attivo
- multi-membership selection rules complete
- organization scope interno
- assignment contestuali
- ACL finale scoped

## Motivazione delle scelte
- Il passaggio riduce il salto tra design e implementazione.
- La nuova piattaforma smette di trattare il context auth come puro placeholder, ma senza aprire ancora il dominio organization in modo pesante.
- La shape di `/api/v1/me` e della condivisione Inertia resta coerente e pronta a crescere.

## Verifiche
- Test API aggiornato per verificare:
  - organization attiva
  - membership attiva
  - scope derivato dall'organization
- Slice pensato per restare compatibile con bootstrap locale Laragon.

## Risultato finale
`MSN-AUTH-003` puo' essere considerato validato:
- esiste una persistence minima reale per organization membership;
- il contesto autenticato del portale puo' ora derivare da dati persistiti;
- il prossimo passo corretto e' rifinire le regole di context organization, non aprire ancora moduli business.

## Step successivo consigliato
Aprire `MSN-ORG-002` con focus su:
- regola di selezione della membership primaria
- gestione di membership multiple
- shape minima di context switching
- relazione tra scope attivo e futura navigation/capability resolution
