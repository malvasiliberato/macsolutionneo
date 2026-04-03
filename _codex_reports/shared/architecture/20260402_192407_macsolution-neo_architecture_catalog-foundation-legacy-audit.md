# Mac Solution Neo - Catalog Foundation Legacy Audit

## Contesto
Dopo il consolidamento della foundation auth/org e dei contratti shared, il passo successivo naturale era osservare il legacy sul bounded context catalogo, senza implementare ancora il modulo catalogo nel nuovo portale.

## Obiettivo del task
Consolidare `MSN-CAT-001` come audit legacy della catalog foundation, separando:
- comportamento legacy osservato;
- indizi utili per il target model Neo;
- rischi da evitare nella transizione.

## Sorgenti legacy ispezionate
- `G:\Mirror\htdocs\macsolution\application\models\Compagnia_model.php`
- `G:\Mirror\htdocs\macsolution\application\models\Prodotti_model.php`
- `G:\Mirror\htdocs\macsolution\application\models\Garanzie_model.php`
- `G:\Mirror\htdocs\macsolution\application\models\Conf_model.php`
- `G:\Mirror\htdocs\macsolution\application\controllers\admin\Compagnia.php`
- `G:\Mirror\htdocs\macsolution\application\controllers\dealer\Utility.php`
- `G:\Mirror\htdocs\macsolution\application\helpers\cvtrelease_helper.php`
- `G:\Mirror\htdocs\macsolution\application\libraries\Cvt_library.php`

## Comportamento legacy osservato

### 1. Il centro operativo e' la compagnia
- `compagnia` appare come entita' principale di configurazione e tariffazione.
- Attorno a `compagnia` ruotano almeno:
  - aree territoriali (`compagnia_aree`)
  - coefficienti per anni (`compagnia_coefficienti_anni`)
  - condizioni associate alle garanzie per compagnia
  - documenti CGA
  - tassi/modificatori

### 2. Il layer "prodotto" non e' pulito
- `Prodotti_model` punta direttamente alla tabella `garanzie`.
- Questo suggerisce che nel legacy il concetto di prodotto/copertura e' in parte collassato dentro `garanzie`.
- La semantica storica di "prodotto" non va quindi presa come verita' architetturale.

### 3. Le abilitazioni dealer fanno parte del comportamento catalogo reale
- `dealer_impostazioni` contiene almeno:
  - `compagnie_abilitate`
  - `dispositivi_abilitati`
- Le compagnie disponibili a un dealer non dipendono solo da una tassonomia catalogo generale, ma anche da configurazioni per dealer.
- Questo e' un comportamento operativo reale da preservare a livello funzionale, ma da modellare meglio nel target Neo.

### 4. Garanzie e valori tariffari sono parte strutturale del catalogo legacy
- `garanzie` copre sia garanzie base sia accessorie.
- `garanzie_valore` collega compagnia, garanzia, area, dispositivo, anni e valori tariffari.
- `garanzie_condizioni` contiene testo/condizioni esposte per compagnia e garanzia.
- Esistono anche casi speciali come GAP con riferimenti `id_compagnia_ref`, talvolta in forma CSV.

### 5. Il catalogo legacy e' fortemente orientato all'uso preventivatore
- `Cvt_library` costruisce il risultato catalogo/quotazione a partire da:
  - dealer corrente
  - compagnie abilitate
  - tipo veicolo
  - area geografica
  - garanzie selezionate
  - dispositivi
- Il comportamento reale e' quindi molto "catalogo configurato per la quotazione", non "catalogo puro" separato.

## Lettura architetturale per Neo

### Cosa conferma il legacy
- `compagnia` e' sicuramente un aggregate o sub-aggregate importante del futuro catalogo.
- Le abilitazioni commerciali/runtime per dealer non possono essere ignorate.
- Il concetto di copertura/garanzia e il concetto di tariffa/configurazione vanno separati meglio di quanto accada nel CI3.

### Cosa non va copiato
- `Prodotti_model -> garanzie`
- riferimenti CSV tipo `id_compagnia_ref`
- mescolanza tra catalogo, regole di quotazione e configurazione dealer nella stessa semantica di dato
- naming storico ambiguo tra prodotto, garanzia, accessoria e compagnia

## Target model prudente raccomandato
Il target Neo dovrebbe restare coerente con una fondazione tipo:
- `fornitore`
- `compagnia`
- `prodotto` o `coverage bundle`
- `copertura/garanzia`
- `pricing configuration`
- `dealer enablement`

L'audit legacy non basta ancora per fissare tutti i boundary finali, ma basta per dire che:
- compagnia e' centrale;
- garanzia non coincide automaticamente con prodotto;
- abilitazioni dealer e configurazioni tariffarie non vanno schiacciate dentro un unico livello semantico.

## Strategia di transizione raccomandata
- Separare nel nuovo modello:
  - struttura catalogo
  - pricing/configurazione
  - enablement commerciale
- Trattare le abilitazioni dealer come capability/configurazione commerciale, non come struttura fondamentale del catalogo.
- Mappare i casi speciali legacy come GAP tramite adapter e regole di migrazione, non tramite replica del dato storico.

## Rischi da evitare
- Copiare il naming storico `prodotto = garanzia`
- Modellare il catalogo Neo direttamente sulle tabelle di pricing legacy
- Portare nel nuovo backend riferimenti CSV o logiche special-case non isolate
- Confondere disponibilita' commerciale per dealer con struttura concettuale del catalogo

## Risultato finale
`MSN-CAT-001` puo' considerarsi validato:
- il legacy fornisce una baseline funzionale sufficiente;
- e' chiaro che il catalogo storico e' fortemente company/garanzia/pricing-driven;
- il prossimo passo corretto e' chiarire il target model catalogo, non implementare ancora il modulo.

## Step successivo consigliato
Aprire `MSN-CAT-002` con output separato in:
- target model catalogo corretto
- boundary tra compagnia, prodotto/copertura e pricing
- strategia di transizione dal catalogo legacy verso Neo
