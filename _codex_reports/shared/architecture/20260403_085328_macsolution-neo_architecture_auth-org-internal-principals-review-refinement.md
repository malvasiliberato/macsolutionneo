# Contesto
Nel filone `AUTH/ORG` il dry-run espone gia' una lettura review-oriented dei principal interni:
- `manual_link_candidate`
- `manual_review_only`
- `do_not_migrate_automatically`
- `manual_target_decision_required`

Il gate di commit resta chiuso per questo lane.

# Obiettivo del task
Raffinare in modo concreto i casi umani plausibili e review-oriented dei principal interni, partendo da:
- `supporto`
- `admin`
- `rosy`

senza riaprire il gate automatico e senza introdurre nuove scritture.

# Casi analizzati
- `supporto`
- `admin`
- `rosy`

# Evidenze legacy rilevanti
## supporto
- In `acl.php` il gruppo `supporto_tecnico` ha:
  - accesso completo area admin;
  - `Error Monitor`;
  - `Tools Support`;
  - diagnostica firma;
  - ACL audit log;
  - gestione dealer, commerciali e backoffice users.
- In `3_left_navigation.php` il menu `Tools Support` e' visibile solo a `supporto_tecnico`.
- Questo lo qualifica come utenza tecnica personale con strumenti extra di monitoraggio e diagnostica, non come utente dealer-scoped.

## admin
- In `acl.php` il gruppo `admin` e' descritto come profilo che gestisce CVT, RCA, Non Auto, finanziamenti, dealer, commerciali e notifiche.
- In `Backoffice_users.php` il gruppo `admin` e' tra i gruppi gestibili della UI backoffice.
- L'evidenza semantica e' forte sul perimetro operativo ampio, ma debole sull'identita' personale reale del principal storico.

## rosy
- In `DefaultAuthOrgLegacyCandidateResolver` `rosy` ricade nel sottotipo `backoffice_operativo`.
- In `acl.php` i gruppi `backoffice_admin`, `backoffice_operatore`, `backoffice_finanziamenti`, `backoffice_rca` descrivono lane operativi di pratiche, approvazioni, RCA e finanziamenti.
- In `Pratiche.php` e nel backoffice admin il comportamento runtime e' coerente con un principal umano operativo di gestione/approvazione, non con un principal tecnico o corporate.
- Manca pero' un segnale legacy abbastanza forte per promuoverla a `manual_link_candidate`.

# Classificazione aggiornata
- `supporto` => `manual_link_candidate`
- `admin` => `manual_review_only`
- `rosy` => `manual_review_only`

# Proposta di handling
## supporto
- Handling proposto: `manual_link_only_if_real_neo_account_exists`
- Motivazione:
  - principal umano plausibile;
  - lane legacy fortemente riconoscibile (`supporto_tecnico`);
  - perimetro tecnico/backoffice molto chiaro;
  - resta assente un segnale abbastanza forte per auto-link.

## admin
- Handling proposto: `manual_review_only`
- Motivazione:
  - ruolo molto ampio e strutturale;
  - naming troppo generico per ricondurlo a una persona;
  - nessuna promozione automatica, nessun link implicito.

## rosy
- Handling proposto: `manual_review_only`
- Motivazione:
  - principal umano plausibile;
  - perimetro operativo coerente col backoffice;
  - ma senza evidenza abbastanza forte verso un target Neo reale gia' identificabile.

# Cosa resta fuori dall'automatismo
- Nessun caso tra `supporto`, `admin`, `rosy` rientra nel lane `create-only`.
- Nessun merge o commit automatico.
- I casi `bo_*` restano `do_not_migrate_automatically`.
- I casi corporate come `mac` restano `manual_target_decision_required`.

# Raccomandazione sul passo successivo
Il passo successivo piu' prudente e' lavorare sul caso `supporto` come primo `manual_link_candidate`, verificando se esiste davvero un account Neo umano da collegare manualmente.

`admin` e `rosy` dovrebbero invece restare nel lane `manual_review_only` finche' non emergono segnali identitari piu' forti.
