# Contesto

Il lane automatico `AUTH/ORG` e' ormai chiuso:

- lane dealer organization chiuso
- lane dealer seller chiuso
- lane commerciale chiuso

Restano solo i principal `dealer admin` legacy e il caso `mac`.

L'obiettivo di questo slice non e' riaprire l'import automatico, ma chiarire il tipo di residui rimasti.

# Obiettivo del task

Definire categorie target leggibili per i `dealer admin` legacy residui, cosi' da evitare che i prossimi prompt riaprano ogni volta la stessa discussione in forma generica.

# Evidenze osservate

Tabella legacy coinvolta:

- `dealer`
- filtro: `tipo = admin`

Campi letti per la baseline:

- `id`
- `username`
- `ragione_sociale`
- `is_enable`
- `password_change`
- `level`

Record osservati:

- `admin`
- `mac`
- `bo_fin_test_01`
- `bo_operatore_01`
- `bo_rca_01`
- `bo_ui_test_01`
- `bo_fin_01`
- `bo_ui_step63`
- `bo_ui_step66`
- `bo_ui_step67`
- `bo_ui_step68`
- `supporto`
- `bo_pwd_step76`
- `bo_pwd_step77`
- `bo_pwd_step78`
- `rosy`

# Categorie target proposte

## 1. Principal tecnici / backoffice / test

Pattern tipico:

- username `bo_*`
- naming esplicitamente tecnico o di test
- principal plausibilmente usati per backoffice, prove UI, password flow o task operativi interni

Esempi:

- `bo_fin_test_01`
- `bo_operatore_01`
- `bo_ui_test_01`
- `bo_pwd_step76`

Decisione:

- non promuoverli automaticamente a utenti Neo
- trattarli come principal legacy tecnici da review manuale o da non migrare

## 2. Principal umani / supporto

Pattern tipico:

- naming che richiama una persona o un operatore reale

Esempi:

- `supporto` con label `Liberato Malvasi`
- `rosy`
- `admin` come principal umano/generico da chiarire

Decisione:

- potenzialmente riconciliabili a utenti Neo esistenti
- solo tramite review manuale esplicita
- nessun auto-match basato solo su username o descrizione

Nota:

- `supporto` e' il caso piu' vicino a un possibile match umano, ma resta fuori dall'automatismo per assenza di identita' forte nativa nel record legacy

## 3. Principal corporate / istituzionali ambigui

Pattern tipico:

- naming che richiama il brand o una pseudo-entita' istituzionale

Esempio chiave:

- `mac` con label `Mac Solution Srl`

Decisione:

- non trattarlo automaticamente come user umano
- non trattarlo automaticamente come organization target
- mantenerlo come caso separato finche' il dominio non chiarisce se sia:
  - principal amministrativo storico
  - pseudo-organization operativa
  - record da non migrare direttamente

# Implicazioni operative

Questa classificazione conferma che:

- il lane automatico `create-only` deve restare chiuso sui `dealer admin`
- il prossimo seguito corretto e' solo manuale o semi-manuale
- il caso `mac` va trattato separatamente dagli altri admin

# Cosa e' stato lasciato invariato volutamente

- nessuna nuova scrittura nel dominio Neo
- nessuna nuova capability o role code
- nessun widening del matcher o del gate

# File aggiornati

- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

# Raccomandazione operativa finale

Se si vuole continuare nel lane `dealer admin`, il prossimo task dovrebbe essere formulato come:

- review/manual reconciliation lane

e non come:

- ulteriore hardening automatico
- nuovo create-only
- merge automatico verso target Neo esistenti
