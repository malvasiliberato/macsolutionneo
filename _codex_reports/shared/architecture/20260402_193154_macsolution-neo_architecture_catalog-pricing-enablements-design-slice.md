# Mac Solution Neo - Catalog Pricing Enablements Design Slice

## Contesto
Il catalog foundation di Neo dispone ora di primitive minime reali:
- supplier
- company
- product
- coverage

Prima di introdurre qualsiasi engine di quotazione o flusso business, serviva chiarire il boundary tra `pricing profile` e `dealer enablement`.

## Obiettivo del task
Definire `MSN-CAT-004` come design slice del catalog foundation su pricing profile ed enablement commerciale.

## Problema ereditato dal legacy
Nel CI3 questi aspetti sono mescolati:
- pricing e coefficienti si appoggiano a tabelle come `garanzie_valore`, `compagnia_aree`, `compagnia_coefficienti_anni`
- disponibilita' commerciale verso dealer passa da `dealer_impostazioni`
- il preventivatore usa tutto insieme come se fosse un unico blocco di catalogo

Neo deve evitare questo collasso.

## Boundary raccomandato

### Pricing profile
`pricing profile` dovrebbe rappresentare:
- regole di prezzo
- durata/anni
- area o territorio
- dispositivo o requirement tecnico
- condizioni economiche o tecniche utili al calcolo

Non dovrebbe rappresentare:
- la struttura del catalogo
- l'abilitazione commerciale verso dealer
- il prodotto in quanto tale

### Dealer enablement
`dealer enablement` dovrebbe rappresentare:
- se una `company` e/o un `product` e' disponibile per un certo dealer o organization
- eventuali vincoli distributivi
- disponibilita' commerciali specifiche del canale

Non dovrebbe rappresentare:
- il prezzo
- le aree tariffarie
- i coefficienti di durata

## Agganci raccomandati nel modello

### Pricing profile
Agganci consigliati:
- `company`
- `product`
- `coverage`
- eventualmente variant/context key tecniche

### Dealer enablement
Agganci consigliati:
- `organization` o futuro aggregate dealer
- `company`
- `product`
- eventualmente scope commerciale o canale

## Regole architetturali da mantenere
- `product` resta struttura catalogo
- `coverage` resta componente di offerta
- `pricing profile` resta layer economico/configurativo
- `dealer enablement` resta layer di disponibilita' commerciale

## Strategia prudente di implementazione
- Non introdurre subito tutte le sfumature del legacy.
- Fare un primo slice tecnico scegliendo uno solo tra:
  - persistence minima del `pricing profile`
  - persistence minima del `dealer enablement`
- Lasciare il secondo layer a uno step successivo se necessario.

## Raccomandazione per il prossimo slice
Tra i due, il primo slice piu' prudente e' spesso `dealer enablement`, perche':
- e' piu' semplice da modellare
- evita di aprire prematuramente il pricing engine
- consente di collegare il catalog foundation alle future organizzazioni/dealer senza entrare nei dettagli tariffari

In alternativa, se serve prima chiarire la semantica economica, si puo' introdurre un `pricing profile` minimale ma non eseguibile.

## Risultato finale
`MSN-CAT-004` puo' essere considerato validato:
- il boundary tra pricing profile e dealer enablement e' chiarito;
- il catalog foundation Neo ha ora una separazione piu' pulita tra struttura, prezzo e disponibilita' commerciale;
- il prossimo passo corretto e' un piccolo slice implementativo tecnico, non il motore di quotazione.

## Step successivo consigliato
Aprire `MSN-CAT-005` con una di queste due opzioni strette:
- `dealer enablement` minimale
- `pricing profile` minimale

Raccomandazione: partire da `dealer enablement` minimale.
