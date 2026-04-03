# Contesto
Nel filone AUTH/ORG il lane automatico `create-only` e' ormai chiuso. Restano solo review residue nel sottoinsieme `dealer.tipo=admin`, gia' separate tra principal tecnici, principal corporate ambigui e principal umani/supporto.

## Obiettivo del task
Consolidare una short-list minima e stabile dei principal umani del lane `dealer admin`, cosi' i prossimi prompt possano partire da categorie gia' pulite senza riaprire il censimento legacy.

## Evidenze usate
- Legacy `dealer.tipo=admin` rilevati:
  - `admin` (`dealer.id=2`, `ragione_sociale=Amministratore`)
  - `mac` (`dealer.id=78`, `ragione_sociale=Mac Solution Srl`)
  - blocco `bo_*`
  - `supporto` (`dealer.id=300`, `ragione_sociale=Liberato Malvasi`)
  - `rosy` (`dealer.id=306`, `ragione_sociale=rosy`)
- Neo:
  - esiste un utente reale con email `liberato.malvasi@hotmail.com`
  - non emergono segnali Neo equivalenti e sufficientemente forti per `admin` o `rosy`

## Short-list consolidata
- `supporto` => `manual_link_candidate`
  - motivazione: esiste un account Neo umano plausibile, ma il segnale non basta per auto-link.
- `admin` => `manual_review_only`
  - motivazione: principal troppo generico, senza identita' personale affidabile.
- `rosy` => `manual_review_only`
  - motivazione: naming umano ma nessun target Neo abbastanza forte per promuoverlo a candidato di link.

## Casi esplicitamente esclusi dalla short-list umana
- `bo_*` e analoghi => `do_not_migrate_automatically`
- `mac` => fuori shortlist, gia' classificato come `manual_target_decision_required`

## Cosa cambia operativamente
- I prossimi task non devono piu' rileggere tutto il lane `dealer admin`.
- Il lane umano puo' partire da una base minima gia' classificata:
  - un solo `manual_link_candidate` concreto (`supporto`)
  - due casi ancora `manual_review_only` (`admin`, `rosy`)
- Il lane tecnico/test e il lane corporate restano separati.

## File aggiornati
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Cosa e' stato volutamente lasciato invariato
- Nessun cambiamento runtime
- Nessun cambiamento al gate `create-only`
- Nessuna nuova scrittura su dominio o mapping
- Nessun auto-link aggiunto

## Limiti
- La short-list non risolve ancora il link manuale di `supporto`
- `admin` e `rosy` restano troppo deboli per qualunque automatismo
- `mac` resta lane separato e non viene riconsiderato qui

## Raccomandazione finale
Il prossimo passo corretto e' un lane di manual reconciliation umano molto stretto, partendo da `supporto` e lasciando `admin` e `rosy` in `manual_review_only` finche' non emergono segnali identitari migliori.
