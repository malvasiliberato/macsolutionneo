# Identity Matching Heuristics Design

## Contesto
Il matcher read-only auth/org esiste gia', ma finora il matching effettivo e' ancora molto vicino al bootstrap statico. Restava da chiarire quali segnali usare per evolvere il matching senza trasformarlo prematuramente in un merge automatico.

## Obiettivo del task
Validare `MSN-AUTH-015` definendo le prime euristiche di matching read-only oltre il bootstrap statico.

## Decisioni adottate
- Le euristiche restano read-only.
- Le euristiche vanno ordinate per affidabilita'.
- Le euristiche non devono saltare direttamente a scritture o merge.

## Famiglie di segnali iniziali

### User
- email/login normalizzato
- legacy mapping tecnico gia' presente
- combinazione prudente tra canale e identificativo legacy

### Organization
- codice dealer o code slug normalizzato
- legacy mapping tecnico gia' presente
- nome normalizzato solo come segnale debole

### Membership
- coppia candidato `user` + candidato `organization`
- ruolo legacy come indizio secondario
- join tecniche o assignment solo se abbastanza stabili semanticamente

## Regole di prudenza
- un solo segnale debole non basta per `matched_existing_target`
- segnali in conflitto devono produrre `ambiguous_match`
- assenza di segnali affidabili deve produrre `no_existing_match` o `needs_review`

## Supporto introdotto
- `app/Application/Auth/LegacyImport/Contracts/AuthOrgLegacyMatchingHeuristics.php`

## Risultato finale
`MSN-AUTH-015` puo' considerarsi validato: il workspace ha ora una base chiara per evolvere il matcher oltre il bootstrap statico, sempre in modalita' read-only.

## Prossimo step consigliato
Aprire `MSN-AUTH-016` per introdurre le prime euristiche concrete nel matcher auth/org read-only.
