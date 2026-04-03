# Contesto

Il filone `AUTH/ORG` ha gia' attraversato:

- reconciliation foundation read-only
- signal hardening
- secondo dry-run reale con esito `GO condizionato`
- commit gate `create-only`
- primo commit controllato reale
- dependency guard sul reconciliation layer

Dopo questi passaggi, il dataset reale `sql1483615_1` non mostra piu' candidati `ready_create` residui.

Restano `19` casi `review-only`, che richiedono una policy esplicita prima di ogni ulteriore commit controllato.

# Obiettivo del task

Fissare una policy prudenziale stabile per i residui reali `AUTH/ORG`, evitando widening del gate e scorciatoie implicite su:

- `dealer admin`
- caso `mac`
- assignment commerciali senza membership prerequisita

# Evidenze reali raccolte

## Breakdown residui

- `16` user review
- `1` membership review
- `2` assignment review

## Motivi review

- `dealer_admin_requires_manual_identity_resolution = 16`
- `membership_candidate_requires_resolved_organization_dependency = 1`
- `assignment_candidate_requires_resolved_membership_dependency = 2`

## Dealer admin

I `16` casi principali sono tutti record `dealer` legacy con:

- `tipo = admin`
- `username` presente
- `ragione_sociale` o label descrittiva presente
- nessuna email affidabile disponibile

Esempi osservati:

- `admin`
- `mac`
- `bo_fin_test_01`
- `bo_operatore_01`
- `supporto`
- `rosy`

Questi record si comportano come principal amministrativi legacy, non come semplici seller dealer-scoped.

## Caso `mac`

Il caso residuo di membership punta a:

- `legacy_table = dealer_collaboratore`
- `legacy_organization_code = mac`
- `legacy_user_username = mac`
- `target_membership_role_code = dealer_admin`

Nel Neo locale:

- non esiste alcuna `organization` con code `mac`

Quindi `mac` oggi non puo' essere trattato automaticamente come dealer-organization target senza introdurre una promozione implicita non giustificata.

## Assignment commerciali residui

I `2` assignment residui puntano a:

- dealer `Macsolution1980@`
- dealer `m.car`
- commerciale `Framal / Franco Malvasi`
- email `liberato.malvasi@hotmail.com`
- ruolo `commerciale`

Nel Neo locale:

- le `organization` dealer esistono
- l'user del commerciale esiste
- manca pero' una `organization_membership` dealer-scoped per quel commerciale verso entrambi i dealer

Il problema residuo quindi non e' il dealer, ma il prerequisito tecnico della membership.

# Policy prudenziale fissata

## 1. Dealer admin legacy

Regola:

- i principal `dealer.tipo=admin` senza identita' forte restano `review-only`

Motivazione:

- non esiste un segnale identitario sufficiente per un import `create-only` affidabile
- generare synthetic identity per questi principal sarebbe piu' invasivo e ambiguo del necessario

## 2. Caso `mac`

Regola:

- `mac` non va promosso implicitamente a `organization` target

Motivazione:

- il record emerge come principal admin legacy
- manca conferma che debba diventare un aggregate `organization` autonomo nel modello target
- una promozione automatica introdurrebbe un coupling architetturale non validato

## 3. Assignment commerciali

Regola:

- nessun assignment viene promosso senza `organization_membership` prerequisita

Motivazione:

- in Neo l'assignment e' un legame operativo sopra una membership, non un sostituto della membership stessa
- chiudere questi casi con una scorciatoia tecnica sporcherebbe il foundation `AUTH/ORG`

# Cosa e' stato lasciato invariato volutamente

- nessun widening del gate `create-only`
- nessun nuovo commit sul dataset reale
- nessuna review UI
- nessun merge con target Neo esistenti
- nessuna creazione artificiale di organization o membership solo per chiudere i residui

# Impatto sulla roadmap

Questa policy sposta il prossimo passo da un generico "altro hardening" a un follow-up piu' chiaro:

1. decidere la semantica target dei principal `dealer admin`
2. decidere se alcuni commerciali debbano poter avere membership dealer-scoped minime
3. tenere il commit gate invariato finche' queste decisioni non sono esplicite

# File aggiornati

- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# Raccomandazione operativa finale

Il prossimo macro-step corretto e' un `AUTH/ORG dealer-admin and commercial-membership review slice`, con due uscite nette:

- policy target per principal admin legacy
- decisione se introdurre una membership minima dedicata per i commerciali usati dagli assignment
