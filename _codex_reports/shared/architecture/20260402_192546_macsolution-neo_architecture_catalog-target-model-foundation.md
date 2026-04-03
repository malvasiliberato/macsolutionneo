# Mac Solution Neo - Catalog Target Model Foundation

## Contesto
L'audit legacy del catalogo ha confermato che nel CI3 il comportamento operativo e' fortemente centrato su:
- compagnie abilitate;
- garanzie/coperture;
- tabelle tariffarie e territoriali;
- configurazioni dealer.

Prima di qualsiasi implementazione catalogo nel nuovo portale serviva chiarire il target model corretto per Neo.

## Obiettivo del task
Definire `MSN-CAT-002` come target model iniziale del catalog foundation, separando struttura catalogo, pricing ed enablement commerciale.

## Problema architetturale osservato nel legacy
- `Prodotti_model` punta a `garanzie`.
- `compagnia` assorbe semantiche di configurazione, tariffazione e contenuto catalogo.
- Le abilitazioni dealer sono mescolate con la disponibilita' del catalogo.
- Il preventivatore usa il catalogo legacy come insieme di regole operative, non come aggregate ben separato.

## Target model raccomandato

### 1. Supplier
- `supplier` rappresenta il soggetto sorgente o mandante del catalogo.
- Non coincide automaticamente con `company`.
- Serve a sostenere il futuro asse `fornitore -> compagnia -> prodotto`.

### 2. Company
- `company` rappresenta l'entita' assicurativa o erogatrice rilevante nel dominio.
- In Neo dovrebbe essere distinta dal prodotto.
- Può avere branding, policy, documentazione e configurazioni generali.

### 3. Product
- `product` rappresenta l'offerta commerciale riconoscibile e governabile.
- Non dovrebbe coincidere con una singola riga tariffaria.
- Non dovrebbe coincidere automaticamente con una singola garanzia.

### 4. Coverage
- `coverage` rappresenta una copertura/garanzia componibile o associabile a uno o piu' prodotti.
- E' il punto naturale in cui assorbire parte della semantica legacy oggi collassata in `garanzie`.
- GAP e casi simili dovrebbero essere trattati come coverage specializzate o coverage option, non come hack di relazione CSV.

### 5. Pricing profile
- `pricing profile` rappresenta configurazioni tariffarie, territoriali, per durata, dispositivo o altri driver di prezzo.
- Deve stare separato dalla struttura catalogo.
- Nel legacy questo layer e' oggi distribuito tra:
  - `garanzie_valore`
  - `compagnia_aree`
  - `compagnia_coefficienti_anni`
  - modificatori e condizioni varie

### 6. Dealer enablement
- `dealer enablement` rappresenta la disponibilita' commerciale del catalogo verso specifici dealer o contesti.
- Non dovrebbe definire la struttura del catalogo.
- Dovrebbe esprimere quali company/product/coverage sono proponibili in uno specifico perimetro commerciale.

## Boundary raccomandati
- `supplier/company/product/coverage` = struttura catalogo
- `pricing profile` = configurazione economica e territoriale
- `dealer enablement` = disponibilita' commerciale e distributiva

Questi tre strati non dovrebbero essere collassati in uno stesso aggregate.

## Traduzione prudente dal legacy
- `compagnia` legacy alimenta soprattutto `company` e parte delle configurazioni generali.
- `garanzie` legacy va spezzata concettualmente tra:
  - `coverage`
  - eventuali primitive di `product composition`
- `garanzie_valore`, `compagnia_aree`, `compagnia_coefficienti_anni` e simili confluiscono nel futuro `pricing profile`.
- `dealer_impostazioni` con `compagnie_abilitate` e `dispositivi_abilitati` suggerisce un futuro layer di `dealer enablement`.

## Cosa non fare
- Non modellare `product = garanzia`.
- Non modellare `company = catalog`.
- Non usare pricing table legacy come struttura primaria del nuovo catalogo.
- Non far dipendere il modello catalogo dal solo preventivatore.

## Strategia di transizione raccomandata
- Introdurre prima le primitive minime del catalog foundation.
- Mappare il legacy tramite adapter:
  - company legacy
  - coverage legacy
  - pricing legacy
  - enablement legacy
- Tradurre i casi speciali come GAP in semantica esplicita nel nuovo modello, non in riferimenti CSV o eccezioni implicite.

## Risultato finale
`MSN-CAT-002` puo' essere considerato validato:
- esiste un target model iniziale prudente e governabile per il catalog foundation;
- il legacy e' baseline funzionale, non disegno da copiare;
- il prossimo passo corretto e' un primo slice implementativo tecnico del catalog foundation, non il preventivatore.

## Step successivo consigliato
Aprire `MSN-CAT-003` con focus su:
- primitive minime persistence del catalog foundation
- naming pulito e separazione di base tra `company`, `product`, `coverage`
- nessun pricing engine completo
- nessun business flow di quotazione
