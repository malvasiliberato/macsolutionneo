# Legacy Import Governance Foundation Deploy Notes

## File/cartelle creati o aggiornati
- `AGENTS.md`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`
- `app/Application/LegacyImport/Contracts/LegacyToTargetMapper.php`
- `app/Application/LegacyImport/Support/LegacyRecordReference.php`

## Namespace introdotti
- `App\Application\LegacyImport\Contracts`
- `App\Application\LegacyImport\Support`

## Classi placeholder introdotte
- `LegacyToTargetMapper`
  - interfaccia minima per tradurre una riga legacy in shape target
- `LegacyRecordReference`
  - supporto tecnico minimale per tracciare source, table, key e legacy id di un record sorgente

## Impatto sul runtime applicativo
- Impatto nullo o minimo.
- Nessuna route, migration, view o comportamento utente e' stato modificato.
- Nessun comando artisan di import e' stato introdotto in questa fase.

## Come usare questa base nei prossimi task
- Documentare sempre schema target, mapping, import strategy e gap.
- Riutilizzare `app/Application/LegacyImport` solo per supporto condiviso.
- Creare codice specifico di import nel bounded context solo quando il task lo richiede davvero.
- Introdurre comandi artisan solo quando esiste gia' un design chiaro dell'import e un perimetro implementativo verificabile.

## Note operative
- Nessuna azione aggiuntiva richiesta per l'avvio locale su Windows + Laragon.
- Il bootstrap Laravel + Vue esistente resta invariato.
- La verifica di successo di questo task e' documentale/strutturale:
  - i file sopra devono esistere nei path indicati;
  - la plancia deve mostrare la governance import come step validato;
  - il prossimo step consigliato deve puntare a un bounded context concreto.
