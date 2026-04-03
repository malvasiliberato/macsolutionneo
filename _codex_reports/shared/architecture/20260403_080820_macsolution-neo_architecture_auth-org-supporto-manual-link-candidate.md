# Contesto

Dopo aver chiuso:

- lane automatico `AUTH/ORG`
- lane commerciale
- decisione target sul caso `mac`

restava da verificare se nel lane umano/supporto esistesse almeno un candidato concreto a riconciliazione manuale verso un account Neo reale.

# Obiettivo del task

Valutare il caso `supporto` come primo candidato concreto del lane:

- `manual_link_only_if_real_neo_account_exists`

senza introdurre auto-link o nuove scritture.

# Evidenze raccolte

## Record legacy

Query su `dealer`:

- `id = 300`
- `tipo = admin`
- `username = supporto`
- `ragione_sociale = Liberato Malvasi`
- `is_enable = 1`

## Collaboratori collegati

Nessun record `dealer_collaboratore` collegato con `dealer.username = supporto`.

Questo rende il principal piu' lineare del caso `mac`: non ha un secondo binario collaboratore che ne complichi ulteriormente la lettura.

## Stato Neo

Nel Neo locale esiste un utente reale:

- `id = 1184`
- `name = Franco Malvasi`
- `email = liberato.malvasi@hotmail.com`

# Valutazione

Il caso `supporto` non e' abbastanza forte per un auto-link, ma e' abbastanza concreto per uscire dalla categoria generica "review indistinta".

Segnali a favore:

- naming legacy umano (`Liberato Malvasi`)
- presenza di utente Neo reale con email coerente col nome legacy

Segnali di prudenza:

- il nome Neo non coincide esattamente (`Franco Malvasi`)
- il record legacy non espone un identificatore forte come email

# Decisione operativa

`supporto` viene classificato come:

- `manual_link_candidate`

cioe':

- non va importato automaticamente
- non va auto-linkato
- puo' essere proposto in un futuro lane manuale come candidato da collegare a un account Neo gia' esistente

# Perche' questa decisione e' utile

Questa decisione:

- evita di tenere `supporto` nello stesso contenitore indistinto dei principal tecnici `bo_*`
- evita anche di forzare un auto-match non sufficientemente fondato
- dimostra che il lane admin puo' produrre candidati umani reviewabili senza riaprire il gate automatico

# File aggiornati

- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# Raccomandazione operativa finale

Se si vuole continuare nel lane manuale, il prossimo step corretto potrebbe essere:

- una short-list di `manual_link_candidate` umani

partendo proprio da `supporto`, senza includere per default i principal tecnici o il caso `mac`.
