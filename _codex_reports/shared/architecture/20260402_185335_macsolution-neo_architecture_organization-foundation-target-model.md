# Organization Foundation Target Model

## Contesto
Il bootstrap reale del portale Mac Solution Neo e il target model iniziale di identity sono gia' stati chiariti. Il passo successivo di design consiste nel definire il boundary organizzativo iniziale, senza introdurre ancora moduli business, gerarchie definitive o ACL finale.

## Obiettivo del task
Definire un target model iniziale e prudente per `MSN-ORG-001`, coerente con il bootstrap attuale e con il target model identity, separando in modo chiaro organization, membership, scope e assignment.

## Stato di partenza osservato nel workspace
- Il bootstrap attuale espone account utente, ruoli bootstrap e capability minime.
- Non esiste ancora un modello organization in persistence o in API.
- La shell UI e la capability foundation attuale sono abbastanza leggere da poter essere riallineate al modello corretto senza refactor distruttivi.

## Distinzione esplicita da mantenere
- Comportamento bootstrap attuale:
  - account autenticato;
  - ruolo tecnico bootstrap;
  - capability minime lato backend.
- Modello target corretto:
  - organization aggregate dedicato;
  - membership esplicita;
  - unit/scope organizzativi;
  - assignment contestuali.
- Strategia di transizione:
  - introdurre il boundary organization senza trasformare `users` nel contenitore di tutta la semantica organizzativa;
  - evitare replica cieca di naming o shortcut legacy.

## Target model organization raccomandato
### 1. Organization aggregate
- `organization` rappresenta l'entita' organizzativa principale rilevante per visibilita', governo e ownership.
- Non va ridotta a semplice etichetta applicata all'utente.
- Attributi iniziali attesi, da confermare in audit:
  - identificativo stabile
  - nome
  - stato attivo/inattivo
  - eventuale tipo o categoria organizzativa

### 2. Membership
- La `membership` rappresenta il legame tra account e organization.
- La membership definisce:
  - appartenenza
  - perimetro minimo di visibilita'
  - eventuale ruolo scoped all'interno della organization
- Un account dovrebbe poter avere piu' membership se il dominio reale lo richiede.

### 3. Organization unit / scope
- Serve un livello di segmentazione interno alla organization.
- Il nome finale puo' essere raffinato in audit, ma il concetto serve gia' adesso:
  - unita' organizzativa
  - area
  - branch
  - team
  - canale o scope equivalente
- Questo layer evita di schiacciare tutte le responsabilita' operative direttamente su organization o user.

### 4. Assignment
- L'`assignment` modella responsabilita' operative contestuali.
- Esempi futuri possibili:
  - assegnazione di un dealer
  - assegnazione di una pratica
  - assegnazione di un account commerciale
- Assignment non equivale a membership:
  - membership dice "appartengo a questo perimetro"
  - assignment dice "sono responsabile di questo oggetto o flusso"

### 5. Ruolo scoped
- Il ruolo nel dominio target dovrebbe essere scoped almeno alla membership o a uno scope organizzativo.
- Un ruolo globale puro va tenuto come eccezione tecnica, non come regola principale del dominio.
- I ruoli bootstrap attuali possono restare tecnici finche' non viene introdotto il modello organizzativo corretto.

## Relazione con Identity
- Identity fornisce l'account autenticabile.
- Organization fornisce appartenenza, scope e responsabilita' operative.
- Capability effettive dovrebbero emergere dalla combinazione di:
  - account
  - membership
  - ruolo scoped
  - assignment
  - policy backend

## Implicazioni per backend, API e UI
- Backend:
  - policy e autorizzazione devono essere membership-aware e assignment-aware.
  - evitare booleani o campi ad hoc su `users` per modellare struttura organizzativa.
- API:
  - `/api/v1/me` in futuro dovrebbe poter restituire organization context attivo, membership e scope correnti.
- UI:
  - sidebar e visibilita' moduli non dovrebbero dipendere solo da ruoli statici.
  - il contesto organizzativo attivo dovra' diventare parte della navigazione e delle policy.

## Cosa non e' stato deciso volutamente in questo step
- tassonomia finale dei tipi di organization
- gerarchia completa delle unita' organizzative
- modello completo di dealer/commerciale/account
- regole legacy dettagliate di mapping utenti-organizzazioni
- ACL finale di dominio

## Rischi da evitare
- usare `users` come surrogate di organization
- modellare organization come solo filtro UI
- confondere membership e assignment
- fare subito tabelle definitive senza audit legacy di riconciliazione

## Risultato dello step
`MSN-ORG-001` puo' essere considerato chiarito a livello di target model iniziale: il nuovo portale dovra' evolvere verso organization aggregate, membership esplicite, scope organizzativi e assignment contestuali.

## Prossimo step consigliato
Aprire `MSN-AUD-001` come audit di riconciliazione identity + organization sul legacy, con output separato in:
- comportamento legacy osservato
- target model identity/org gia' chiarito
- strategia di transizione compatibile e graduale
