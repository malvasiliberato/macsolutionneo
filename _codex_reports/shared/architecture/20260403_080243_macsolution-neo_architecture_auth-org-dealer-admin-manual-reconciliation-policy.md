# Contesto

Il lane automatico `AUTH/ORG` e' ormai chiuso.

Residui correnti:

- `16` principal `dealer.tipo=admin`
- `1` caso `mac` come membership `dealer_admin` dipendente da organization non definita

Questo slice non implementa UI o nuovi commit.

Serve a fissare una policy manuale stabile per evitare che i prossimi prompt riaprano in modo ambiguo il tema `dealer admin`.

# Obiettivo del task

Definire l'esito operativo raccomandato per ciascuna sottocategoria del lane `dealer admin` legacy.

# Categorie e policy

## 1. Principal tecnici / test / backoffice

Pattern:

- `bo_*`
- principal di test
- principal evidentemente tecnici o di supporto operativo storico

Esempi:

- `bo_fin_test_01`
- `bo_operatore_01`
- `bo_ui_test_01`
- `bo_pwd_step76`

Esito operativo:

- `do_not_migrate_automatically`

Motivazione:

- non rappresentano in modo affidabile attori target del nuovo portale
- sono troppo legati a contesti tecnici o storici del CI3

## 2. Principal umani / supporto

Pattern:

- naming umano o riconducibile a una persona reale

Esempi:

- `supporto`
- `rosy`
- `admin` quando usato come principal personale e non come shared admin account

Esito operativo:

- `manual_link_only_if_real_neo_account_exists`

Motivazione:

- il target corretto non e' creare un nuovo principal automatico
- il target corretto, se esiste, e' collegare il principal legacy a un account Neo reale gia' verificato

Nota:

- il caso `supporto` e' il miglior candidato a questa policy, anche per la presenza di `Liberato Malvasi` nel naming legacy e di un utente Neo reale gia' esistente
- questo non autorizza pero' alcun auto-link automatico

## 3. Principal corporate / istituzionali ambigui

Pattern:

- principal non chiaramente umano
- naming corporate o pseudo-istituzionale

Esempio:

- `mac`

Esito operativo:

- `manual_target_decision_required`

Motivazione:

- non e' corretto trattarlo automaticamente come utente umano
- non e' corretto trattarlo automaticamente come organization
- richiede una decisione di dominio/transition esplicita

# Regole permanenti del lane

- nessun nuovo widening del gate `create-only`
- nessuna synthetic identity per chiudere i principal admin
- nessun auto-link verso utenti Neo solo per similarita' nominale
- ogni riconciliazione nel lane admin deve essere esplicita e tracciabile

# Cosa cambia nei prossimi prompt

Da ora i prompt successivi possono assumere queste tre uscite standard:

- `do_not_migrate_automatically`
- `manual_link_only_if_real_neo_account_exists`
- `manual_target_decision_required`

Questo evita di ridecidere ogni volta lo stesso perimetro.

# File aggiornati

- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# Raccomandazione operativa finale

Se si continua davvero nel lane `dealer admin`, il prossimo step non dovrebbe essere un nuovo import slice, ma uno di questi due:

1. `dealer-admin manual link candidates`
2. `case mac target decision`
