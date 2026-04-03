# Contesto

Nel filone `AUTH/ORG` il perimetro residuo dopo `create-only` e dependency guard e' ormai ridotto a `19` review:

- `16` principal `dealer admin`
- `1` membership sul caso `mac`
- `2` assignment commerciali senza membership prerequisita

Il punto da chiarire non era piu' il matcher, ma il target corretto di questi residui.

# Obiettivo del task

Fissare una decisione target minima e stabile su:

- come trattare i `dealer admin` legacy
- come trattare gli assignment commerciali residui

senza allargare il gate `create-only`, senza introdurre merge aggressivi e senza aprire moduli business.

# Evidenze usate

## Dealer admin legacy

Le evidenze raccolte sul legacy mostrano che i `16` residui principali sono tutti record `dealer` con:

- `tipo = admin`
- `username` presente
- nessuna email affidabile
- naming da principal amministrativo/backoffice (`admin`, `supporto`, `bo_*`, `rosy`, `mac`)

Questo li rende semanticamente diversi da:

- `dealer` come `organization`
- `dealer_collaboratore` come `user + membership` dealer-scoped

## Assignment commerciali

I `2` assignment review puntano a:

- dealer `Macsolution1980@`
- dealer `m.car`
- commerciale `Framal / Franco Malvasi`
- email `liberato.malvasi@hotmail.com`

Nel Neo locale:

- il `user` esiste
- i due dealer-organization esistono
- manca la `organization_membership` del commerciale sui due dealer

Quindi l'assignment e' semanticamente comprensibile, ma tecnicamente non puo' esistere senza il suo prerequisito.

# Decisione architetturale

## 1. Dealer admin legacy

Decisione:

- i principal `dealer.tipo=admin` non rientrano nel lane `dealer-scoped create-only`

Interpretazione target:

- sono principal amministrativi legacy da ricondurre a un lane dedicato successivo
- non vanno reinterpretati come `dealer_seller`
- non vanno forzati come `dealer_admin membership` su un dealer implicito

Conseguenza pratica:

- restano `review-only`
- il caso `mac` resta fuori dal perimetro organization create-only

## 2. Assignment commerciali

Decisione:

- `dealer_operator_assignments` resta un layer sopra `organization_memberships`

Interpretazione target:

- un commerciale puo' essere assegnato a un dealer solo se esiste prima una membership minima esplicita su quel dealer
- l'assignment non deve creare o simulare quella membership

Conseguenza pratica:

- i `2` assignment residui non vanno chiusi nel gate attuale
- il prossimo slice corretto, se si vuole trattarli, e' una foundation minima per membership dei commerciali verso dealer

# Cosa cambia nella gestione dei prossimi prompt

Da ora il filone `AUTH/ORG` puo' assumere come regola stabile:

- `dealer admin` legacy = principal amministrativi review-only, fuori dal lane dealer-scoped create-only
- `assignment` senza membership prerequisita = review-only, non trattabile con scorciatoie

Questo riduce il rischio di:

- reinterpretare principal storici come organization senza decisione esplicita
- usare `dealer_operator_assignments` per colmare lacune di membership

# Cosa e' stato volutamente rimandato

- nessuna implementazione di lane dedicato per i `dealer admin`
- nessuna review UI
- nessuna estensione del gate `create-only`
- nessuna introduzione automatica di membership per i commerciali

# File aggiornati

- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# Raccomandazione operativa finale

Il prossimo macro-step corretto e':

- `AUTH/ORG commercial membership minimum slice`

con obiettivo stretto:

- introdurre solo il minimo necessario per rappresentare in modo intenzionale la membership dealer-scoped dei commerciali usati negli assignment

lasciando invariato il lane review-only dei `dealer admin` legacy.
