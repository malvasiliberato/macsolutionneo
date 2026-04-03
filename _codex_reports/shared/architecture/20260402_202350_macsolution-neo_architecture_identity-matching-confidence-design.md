# Contesto
Mac Solution Neo sta costruendo in modo progressivo il filone `legacy -> Neo` per `Identity + Organization`, mantenendo il comando `legacy:import:auth-org` in perimetro read-only e senza aprire ancora commit mode o import completi.

# Obiettivo del task
Chiarire il primo livello di confidence scoring del matching auth/org legacy, in modo che i prossimi slice possano distinguere meglio tra match forti, medi, deboli e non affidabili.

# Problema da risolvere
`match_status` e `match_reason` non bastano da soli a esprimere la qualita' del match. Serve una metrica prudente che aiuti:
- il dry-run tecnico;
- la futura riconciliazione;
- la decisione su quando restare in `needs_review` o `needs_reconciliation`.

# Principi adottati
- Il confidence scoring resta read-only.
- La confidence non sostituisce `match_status`, ma lo qualifica.
- I segnali deboli o ambigui non devono essere promossi artificialmente.
- Il mapping tecnico gia' validato puo' alzare la confidenza, ma non deve nascondere collisioni o edge case.

# Convenzione proposta
- Livelli iniziali ammessi:
  - `high`
  - `medium`
  - `low`
  - `none`
- Lettura pratica:
  - `high`: match forte e coerente, con segnali robusti o mapping tecnico verificato;
  - `medium`: match plausibile con segnale solido ma senza conferma ridondante;
  - `low`: match debole o parziale, utile solo come indicazione prudente;
  - `none`: nessuna base sufficiente per considerare il match affidabile.

# Regole di prudenza
- `ambiguous_match` non deve mai avere confidence `high`.
- `collision`, `unresolved`, stop condition o weak-signal-only devono degradare la confidence.
- La confidence va sempre letta insieme a:
  - `candidate_type`
  - `resolution_status`
  - `match_status`
  - `match_reason`
  - `match_strategy`

# Segnali da usare nei prossimi slice
- Segnali forti:
  - mapping tecnico legacy gia' registrato e coerente;
  - chiave forte normalizzata univoca come email o code nel contesto corretto;
  - pair matching coerente per membership.
- Segnali medi:
  - un solo segnale robusto senza conferma ridondante;
  - contesto coerente ma non pienamente disambiguato.
- Segnali deboli:
  - nome normalizzato da solo;
  - segnali parziali o incompleti;
  - presenza di un solo lato della relazione membership.

# Struttura introdotta nel workspace
- Contratto aggiunto:
  - `app/Application/Auth/LegacyImport/Contracts/AuthOrgLegacyMatchingConfidence.php`

# Cosa e' stato volutamente lasciato fuori
- Nessuna implementazione runtime del confidence scoring
- Nessuna modifica al matcher o al dry-run
- Nessuna scrittura su target Neo o mapping table
- Nessun commit mode

# Perche' questo step prepara il successivo
Questo design consente di aprire `MSN-AUTH-020` in modo stretto e verificabile: il prossimo slice potra' aggiungere `match_confidence` e metriche aggregate senza cambiare il contratto generale del comando o aprire import reali.

# Rischi e limiti
- La confidence resta una metrica tecnica, non una decisione di business autonoma.
- Se usata senza `match_status`, puo' essere interpretata in modo eccessivo.
- Le soglie esatte potranno richiedere taratura dopo i primi dataset legacy reali.

# Raccomandazione operativa finale
Aprire `MSN-AUTH-020` e introdurre il confidence scoring direttamente nel dry-run auth/org, mantenendo perimetro read-only e output tecnico verificabile.
