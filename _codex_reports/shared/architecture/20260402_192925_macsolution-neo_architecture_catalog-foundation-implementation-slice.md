# Mac Solution Neo - Catalog Foundation Implementation Slice

## Contesto
Il workspace Neo disponeva gia' di:
- audit legacy catalogo;
- target model iniziale del catalog foundation.

Serviva ora introdurre un primo slice tecnico reale nel nuovo portale, ma senza pricing engine, preventivatore o flussi business.

## Obiettivo del task
Implementare `MSN-CAT-003` come foundation persistence minima del catalogo.

## Scelte applicate

### Primitive introdotte
- `Supplier`
- `Company`
- `Product`
- `Coverage`
- pivot `coverage_product`

### Relazioni minime
- `supplier -> companies`
- `company -> products`
- `product <-> coverages`

### Cosa e' stato volutamente escluso
- pricing profile
- dealer enablement persistito
- regole territoriali
- configurazioni tariffarie
- preventivatore
- API catalogo finali

## Motivazione
- Il target model catalogo aveva bisogno di primitive reali per smettere di restare solo documentale.
- Il layer minimo introdotto evita di collassare `product` e `coverage`, che e' uno dei principali errori da non portare dal legacy.
- Il perimetro resta stretto e governabile.

## Seed bootstrap
Il seed locale ora crea un set minimo dimostrativo:
- supplier `macsupplier`
- company `neo-insure`
- product `cvt-protection`
- coverages `furto-incendio` e `gap`

Questo serve solo a rendere il foundation verificabile, non a rappresentare il catalogo reale finale.

## Verifiche
- test dedicato `CatalogFoundationTest`
- verifica della separazione tra product e coverage
- relazioni base funzionanti

## Risultato finale
`MSN-CAT-003` puo' essere considerato validato:
- esistono primitive minime reali del catalog foundation;
- il naming del nuovo portale e' pulito e non ricalca il collasso legacy;
- il prossimo passo corretto e' chiarire pricing profile e dealer enablement, non aprire ancora il preventivatore.

## Step successivo consigliato
Aprire `MSN-CAT-004` con focus su:
- target model pricing profile
- target model dealer enablement
- strategia di collegamento con company/product/coverage
