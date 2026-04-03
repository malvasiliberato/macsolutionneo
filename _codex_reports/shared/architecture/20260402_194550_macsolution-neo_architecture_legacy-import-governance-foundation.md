# Legacy Import Governance Foundation

## Contesto
Mac Solution Neo sta evolvendo per bounded context progressivi, ma finora la governance permanente del rapporto tra DB legacy e nuovo schema Neo non era ancora esplicitata come filone strutturale del workspace.

## Obiettivo del task
Definire una base tecnica e documentale minima, stabile e riusabile per tutti i futuri task di import legacy -> Neo, senza implementare ancora import completi dei moduli business.

## Problema da risolvere
- Evitare che i bounded context nuovi nascano scollegati dal problema reale del caricamento dati dal legacy.
- Evitare mapping impliciti o dispersi tra report, codice e note locali.
- Distinguere in modo ripetibile tra schema target, mapping, strategia di import e gap non riconciliati.

## Principi adottati
- Il legacy e' baseline funzionale e anche sorgente dati di riferimento nella transizione.
- Il nuovo schema non replica ciecamente il legacy, ma deve restare riconciliabile con esso.
- Ogni bounded context con impatto dati deve lasciare traccia esplicita di:
  - schema target;
  - mapping legacy -> target;
  - strategia di importazione o migrazione;
  - gap, collisioni, ambiguita' e record non mappabili automaticamente.
- La governance deve restare essenziale: nessun framework ETL astratto introdotto in questa fase.

## Struttura proposta
- Report di mapping e strategia import:
  - `_codex_reports/shared/architecture`
- Report operativi o note di esecuzione import:
  - `_codex_reports/infra/deploy`
- Supporto tecnico condiviso:
  - `app/Application/LegacyImport`
- Supporto specifico di bounded context:
  - `app/Application/<Context>/LegacyImport`

## Convenzioni definite
- Naming file mapper:
  - `Legacy<Context><Entity>Mapper.php`
- Naming orchestratori import:
  - `Import<Context><Entity>FromLegacy.php`
- Naming support shared:
  - `LegacyRecordReference`, `LegacyImportResult`, `LegacySourcePointer`
- Naming comandi artisan:
  - `legacy:import:<context>`
  - `legacy:dry-run:<context>`
- Naming report:
  - architettura/mapping: `YYYYMMDD_HHMMSS_macsolution-neo_architecture_<topic>.md`
  - operativo/deploy/import: `YYYYMMDD_HHMMSS_macsolution-neo_deploy_<topic>.md`

## Standard di mapping/import
- Ogni contesto deve distinguere almeno cinque categorie:
  - seed statici
  - bootstrap dati minimi
  - import una tantum dal legacy
  - sync transitorio
  - reimport idempotente
- Il mapping va documentato come trasformazione esplicita dal dato storico al target Neo, non come copia tabellare.
- La strategia di import deve chiarire almeno:
  - unit of work
  - idempotenza
  - riconciliazione
  - dipendenze tra entita'
  - casi non mappabili automaticamente

## Standard di tracking legacy references
- `legacy_id`: solo per riferimenti semplici e uno-a-uno.
- `legacy_source`: quando esistono piu' sorgenti o la provenienza non e' implicita.
- `legacy_table` e `legacy_key`: solo se servono a ricostruire provenienza o chiave composta.
- Mapping table dedicata:
  - quando il mapping non e' uno-a-uno;
  - quando serve supportare reimport idempotente;
  - quando piu' sorgenti confluiscono nello stesso aggregate;
  - quando il riferimento legacy non e' appropriato come attributo diretto del dominio.
- Tracking tecnico esterno al dominio:
  - quando il riferimento legacy serve soprattutto a import, audit o riconciliazione e non al linguaggio di dominio.

## Standard di reportistica
Ogni futuro task con impatto dati deve produrre almeno:
- schema target
- mapping legacy -> target
- strategia di import
- gap, collisioni, ambiguita' e dati non mappabili automaticamente

## Cosa e' stato creato o aggiornato nel workspace
- Aggiornato `AGENTS.md` con regole permanenti e convenzioni import legacy.
- Aggiornato `AI_CONTEXT.md` con memoria stabile del filone legacy import -> Neo.
- Aggiornato `MASTER_PROGRESS.md` con step validato di governance e prossimo focus consigliato.
- Creato supporto shared minimale:
  - `app/Application/LegacyImport/Contracts/LegacyToTargetMapper.php`
  - `app/Application/LegacyImport/Support/LegacyRecordReference.php`

## Come usare questa governance nei prossimi bounded context
- Prima chiarire lo schema target del bounded context.
- Poi esplicitare il mapping dal legacy al target.
- Poi definire la strategia di importazione o reimport.
- Infine documentare gap, collisioni, casi manuali o non riconciliati.
- Solo dopo questi passaggi aprire eventuali slice implementativi di import o sync.

## Rischi e limiti
- La governance da sola non sostituisce l'audit puntuale dei dati legacy.
- I placeholder introdotti non sono ancora un runtime import completo.
- Alcuni bounded context potranno richiedere mapping table dedicate o tracking tecnico piu' ricco, da decidere caso per caso.

## Raccomandazione operativa finale
Applicare subito questa governance a `Identity + Organization`, perche' e' il bounded context piu' trasversale e gia' parzialmente chiarito a livello target model. Questo consente di aprire il primo design di mapping/import con valore immediato per tutti i filoni successivi.
