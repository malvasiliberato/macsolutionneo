# Identity Legacy Import Dry-Run Slice

## Contesto
Il workspace aveva gia' chiarito il contratto minimo del comando auth/org, ma non esisteva ancora un'esecuzione tecnica reale e verificabile del `dry-run`.

## Obiettivo del task
Implementare `MSN-AUTH-008` introducendo un primo comando tecnico:
- `legacy:import:auth-org --dry-run`

senza collegarsi ancora al DB legacy reale e senza scrivere su target o mapping.

## Scelte implementative
- Introdotto comando console `AuthOrgLegacyImportCommand`
- Introdotto servizio minimale `AuthOrgLegacyDryRun`
- Usato dataset controllato `bootstrap-auth-org`
- Mantenuta la modalita' solo read-only in questa fase

## Contratto concretamente verificato
- input:
  - `--source-system`
  - `--legacy-table`
  - `--dataset`
  - `--batch`
  - `--dry-run`
- output tecnico:
  - riepilogo key=value con record letti, candidati, collisioni, unresolved e batch result
- sicurezza:
  - commit mode rifiutata esplicitamente
  - nessuna scrittura su aggregate target
  - nessuna scrittura su `legacy_entity_mappings`

## Motivazione delle scelte
- Il dataset controllato consente di verificare subito il contratto del comando senza dipendere dal DB legacy.
- L'output semplice key=value e' leggibile, testabile e adatto ai prossimi step.
- Il rifiuto del commit mode evita widening e impedisce usi prematuri del comando come import reale.

## Cosa e' stato volutamente rimandato
- adapter DB legacy reale
- trasformazione reale delle righe CI3
- scrittura su target Neo
- scrittura su `legacy_entity_mappings`
- orchestrazione di import per batch reali

## Risultato finale
`MSN-AUTH-008` puo' considerarsi validato:
- il comando esiste davvero;
- il dry-run e' eseguibile;
- il comportamento e' read-only e testato;
- il contratto resta stabile per il prossimo adapter reale.

## Prossimo step consigliato
Aprire `MSN-AUTH-009` per progettare il primo adapter legacy reale:
- stessa shape di input
- stesso output tecnico
- dataset controllato mantenuto come fallback di sicurezza
