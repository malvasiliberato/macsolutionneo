# Contesto

Nel lane residuo `dealer admin` il caso piu' ambiguo era `mac`.

Fino a questo punto `mac` risultava:

- principal `dealer.tipo=admin`
- residuo `review-only`
- causa indiretta del residuo membership `dealer_admin` con `legacy_organization_code = mac`

Serviva quindi una decisione target esplicita per evitare ulteriori ambiguita' nei prossimi prompt.

# Obiettivo del task

Fissare una decisione target unica e prudente sul caso `mac`.

# Evidenze raccolte

## Dealer legacy

Record osservato:

- `id = 78`
- `tipo = admin`
- `username = mac`
- `ragione_sociale = Mac Solution Srl`
- `is_enable = 1`
- `password_change = 0`
- `level = 1`

Questo lo colloca nel lane `principal admin legacy`, non nel lane `dealer organization` gia' importato automaticamente.

## Collaboratore legacy collegato

Esiste anche un `dealer_collaboratore` collegato:

- `id = 120`
- `id_dealer = 78`
- `username = mac`
- `ruolo = amministratore`
- `dealer_code = mac`
- `dealer_name = Mac Solution Srl`

Questo conferma che il legacy collassa sullo stesso codice `mac` sia il principal admin sia il contesto collaboratore, aumentando l'ambiguita' invece di ridurla.

## Stato Neo attuale

Nel Neo locale:

- non esiste `organization.code = mac`
- non esiste membership su organization `mac`
- non esiste utente Neo denominato `mac`

Quindi il sistema target non contiene oggi un equivalente canonico che autorizzi una promozione automatica.

# Decisione target

`mac` va trattato come:

- `principal amministrativo legacy corporate-ambiguous`

e quindi:

- non va promosso automaticamente a `organization`
- non va promosso automaticamente a `user`
- non va usato come base implicita per creare una membership `dealer_admin`

# Motivazione

Promuovere `mac` ad `organization` sarebbe scorretto perche':

- il record sorgente e' nel lane `dealer.tipo=admin`
- il significato operativo nel legacy e' piu' vicino a principal amministrativo che a dealer target normale

Promuovere `mac` a `user` sarebbe scorretto perche':

- non c'e' identita' personale forte
- la label `Mac Solution Srl` e' corporate, non umana

Creare una membership `dealer_admin` verso `mac` sarebbe scorretto perche':

- mancherebbe una `organization` target validata
- si introdurrebbe un aggregate implicito nato solo per far passare l'import

# Regola operativa risultante

Per `mac` l'unico esito corretto e':

- `manual_target_decision_required`

Possibili esiti futuri, ma solo tramite decisione esplicita:

1. non migrare il record
2. mappare il record a un principal amministrativo interno non dealer-scoped
3. definire un aggregate dedicato, se il dominio futuro lo giustifichera'

# Impatto sui residui

Questa decisione conferma che il residuo:

- `membership_candidate_requires_resolved_organization_dependency`

non va risolto introducendo una organization `mac` automatica.

Il residuo resta corretto e voluto finche' non esiste una decisione manuale esplicita.

# File aggiornati

- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# Raccomandazione operativa finale

Il lane `dealer admin` puo' ora considerare `mac` come caso separato chiuso a livello decisionale.

Se si continua, il prossimo seguito corretto e':

- selezionare eventuali principal umani come `supporto` per un possibile `manual_link_only_if_real_neo_account_exists`
