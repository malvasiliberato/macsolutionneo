# Mac Solution Neo - Catalog Dealer Enablement Implementation Slice

## Contesto
Il design del catalog foundation aveva chiarito che `dealer enablement` doveva restare separato da:
- struttura catalogo
- pricing profile

Serviva ora un primo slice tecnico reale ma minimo, senza pricing engine e senza flussi business.

## Obiettivo del task
Implementare `MSN-CAT-005` nella variante prudente `dealer enablement` minimale.

## Scelta adottata
- Introdotta la relazione `organization_product_enablements`.
- L'enablement collega:
  - `organization`
  - `product`
- `company` resta derivabile dal prodotto, evitando duplicazioni e inconsistenze immediate nel foundation.

## Motivazione
- Il dealer enablement minimale e' piu' leggero e governabile del pricing profile.
- Permette di collegare il foundation catalogo alla foundation auth/org senza aprire il motore di quotazione.
- Mantiene separato il boundary commerciale dal boundary economico.

## Modifiche applicate
- Nuovo model `OrganizationProductEnablement`
- Nuova migration `organization_product_enablements`
- Relazioni aggiunte su:
  - `Organization`
  - `Product`
- Seeder bootstrap aggiornato per abilitare il prodotto demo sull'organization workspace bootstrap
- Test dedicato esteso per verificare la semantica di availability commerciale

## Cosa e' stato volutamente lasciato fuori
- enablement a livello company-only
- filtri per coverage
- date di validita' commerciali
- pricing profile
- engine di quotazione

## Risultato finale
`MSN-CAT-005` puo' essere considerato validato:
- esiste un primo layer persistito di disponibilita' commerciale del catalogo;
- il foundation resta pulito e non introduce pricing prematuro;
- il prossimo passo corretto e' chiarire il target model minimo del pricing profile.

## Step successivo consigliato
Aprire `MSN-CAT-006` con focus su:
- pricing profile minimale
- agganci a company/product/coverage
- esclusione esplicita di ogni engine di quotazione completo
