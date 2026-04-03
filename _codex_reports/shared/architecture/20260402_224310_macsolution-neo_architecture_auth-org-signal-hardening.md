# Auth/Org Signal Hardening

## Contesto
Nel filone `AUTH/ORG` erano gia' presenti:
- correction slice sui venditori dealer come attori autenticabili di primo livello;
- foundation di import reconciliation read-only;
- dry-run reale sul dataset legacy `sql1483615_1` con esito `NO-GO`.

Il dry-run reale aveva mostrato soprattutto:
- email mancanti, sporche o placeholder;
- dealer admin con segnali identitari deboli;
- pairing membership troppo fragile;
- eccesso di casi `ambiguous_match` o `needs_review`.

## Obiettivo del task
Rafforzare i segnali del layer di reconciliation `AUTH/ORG` senza introdurre commit mode, import massivo o nuovi moduli, preparando in modo prudente un secondo dry-run reale comparabile e piu' leggibile.

## Problemi del dry-run precedente
- `dealer_collaboratore` spesso con email invalide o placeholder, ma con `username` reale e relazione chiara col dealer.
- `dealer admin` legacy talvolta privo di email affidabile, quindi troppo facile da confondere con un normale candidato `user`.
- membership candidate troppo dipendenti dal solo pairing `email + organization_code`.
- reporting che non distingueva abbastanza tra:
  - veri casi da review;
  - casi `blocked`;
  - casi `ready_create` prudenziali ma senza match esistente.

## Regole di hardening introdotte
### Identity normalization
- placeholder identity trattati come non affidabili:
  - `-`
  - `--`
  - `null`
  - `n/a`
  - `na`
  - `nessuna`
  - `nessuno`
- email e username vengono ora puliti in modo coerente prima di usarli come segnali.

### Venditori dealer
- `dealer_collaboratore` resta sorgente primaria per utenti dealer-scoped.
- Se l'email legacy e' placeholder o mancante, il candidato puo' comunque restare trattabile come `ready_create` solo se esiste un segnale prudente:
  - `dealer_code + username`
  - ruolo target coerente (`dealer_seller` o `dealer_admin`)
- Questo non promuove il candidato a auto-match: lo rende solo meno ambiguo come candidato di creazione prudente.

### Dealer admin case
- I `dealer admin` legacy senza identita' forte restano caso speciale:
  - `ambiguous_match`
  - `dealer_admin_requires_manual_identity_resolution`
- Non vengono promossi automaticamente a `ready_create` per semplice presenza di username dealer.

### Membership pairing hardening
- Pairing forte confermato:
  - `legacy_user_email + legacy_organization_code`
- Pairing prudenziale introdotto per `dealer_collaboratore`:
  - `dealer_code + username`
- Pairing incompleto resta in review:
  - organizzazione senza identita' utente;
  - identita' utente senza organizzazione;
  - segnali placeholder o deboli.

### Reporting migliorato
- `manual_review_candidates` ora conta solo i veri casi `needs_review`.
- `blocked_candidates` resta separato.
- introdotti breakdown tecnici:
  - `review_reason_breakdown`
  - `blocked_reason_breakdown`

## Casi speciali trattati
- pseudo-email dei venditori dealer
- seller dealer-scoped con username reale e dealer noto
- dealer admin con identita' non abbastanza forte
- pairing membership dealer-scoped senza email valida ma con username utile

## Impatto atteso sul matching
- meno falsi ambigui dovuti solo a placeholder email
- migliore trattamento dei candidati venditore dealer-scoped
- migliore distinzione tra:
  - `ready_create` prudenziale
  - `needs_review`
  - `blocked`
- maggiore leggibilita' del prossimo dry-run reale, soprattutto per `user` e `membership`

## Esempio verificato nel dataset controllato di hardening
Comando:
```powershell
php artisan legacy:import:auth-org --dry-run --source-system=legacy_ci3 --legacy-table=all --dataset=bootstrap-auth-org-signal-hardening --batch=10
```

Esito osservato:
- `ready_create_candidates = 2`
- `manual_review_candidates = 1`
- `review_reason_breakdown = {"dealer_admin_requires_manual_identity_resolution":1}`

Questo conferma il comportamento desiderato:
- il venditore dealer-scoped con placeholder email ma `dealer_code + username` resta trattabile;
- il dealer admin senza identita' forte resta in review.

## Cosa e' stato volutamente rimandato
- commit mode
- review UI
- sync o import incrementale
- regole finali ACL
- refactor completo del domain `Organization`

## Readiness al secondo dry-run
La base e' ora pronta per un secondo dry-run reale piu' affidabile.

La raccomandazione resta prudente:
- rieseguire prima il dry-run reale;
- confrontare metriche con il run precedente;
- riaprire il commit gate solo se il volume di `needs_review` e `ambiguous_match` scende in modo leggibile e motivato.
