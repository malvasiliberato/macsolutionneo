# Mac Solution Neo - Identity + Organization Legacy Audit

## Contesto
Il workspace Mac Solution Neo dispone gia' di un bootstrap reale Laravel + Vue, di un target model iniziale per identity e di un target model iniziale per organization. Prima di aprire implementazioni foundation nel nuovo portale era necessario verificare il comportamento legacy effettivo su autenticazione, ruoli, permessi, perimetro organizzativo e assignment operativi.

## Obiettivo del task
Consolidare `MSN-AUD-001` con un audit rapido ma reale del legacy CodeIgniter 3, separando:
- comportamento legacy osservato;
- modello target gia' chiarito nel workspace Neo;
- implicazioni di transizione da usare nei prossimi step.

## Sorgenti legacy ispezionate
- `G:\Mirror\htdocs\macsolution\application\core\Main_admin.php`
- `G:\Mirror\htdocs\macsolution\application\core\Main_dealer.php`
- `G:\Mirror\htdocs\macsolution\application\libraries\Auth.php`
- `G:\Mirror\htdocs\macsolution\application\models\Auth_acl_model.php`
- `G:\Mirror\htdocs\macsolution\application\controllers\admin\Login.php`
- `G:\Mirror\htdocs\macsolution\application\controllers\Api.php`
- `G:\Mirror\htdocs\macsolution\application\models\Dealer_commerciale_assignment_model.php`
- `G:\Mirror\htdocs\macsolution\application\models\Dealer_operatore_figura_model.php`
- `G:\Mirror\htdocs\macsolution\application\migrations\20260327193000_acl_foundation.php`
- `G:\Mirror\htdocs\macsolution\application\migrations\20260328223000_create_commerciali_table.php`
- `G:\Mirror\htdocs\macsolution\application\migrations\20260402113100_create_dealer_operatore_figura_table.php`
- `G:\Mirror\htdocs\macsolution\application\helpers\session_helper.php`

## Comportamento legacy osservato

### 1. Gli ingressi identitari non sono unificati
- Il backoffice `admin` autentica tramite `admin/Login.php` usando record presenti nella tabella `dealer` con `tipo = admin`.
- L'area `dealer` usa una sessione separata e carica due oggetti distinti:
  - `dealer`
  - `user_logged`
- L'API legacy usa JWT separati e tratta di fatto il dealer come principal applicativo, non un account unificato multi-contesto.

### 2. L'autenticazione legacy e' session-centric e canale-specifica
- `Main_admin` verifica `is_logged_in('admin')` e redirige a `admin/login`.
- `Main_dealer` verifica `is_logged_in('dealer')` e redirige a `dealer/login`.
- `session_helper.php` mostra che la nozione di login e' legata a chiavi di sessione diverse per area.
- La funzione `is_logged_in()` restituisce `TRUE` per richieste API rilevate per URI, segnale di distinzione tecnica forte tra canale web e canale API.

### 3. ACL presente ma innestata su un modello legacy eterogeneo
- `Auth_acl_model` introduce un layer utile con:
  - gruppi
  - permessi
  - assegnazioni gruppi-utenti
  - override diretti utente-permesso
- L'ACL distingue gli utenti tramite coppia `user_type + user_id`, non tramite un solo aggregate account.
- Le migrazioni ACL recenti confermano che questo layer e' un'evoluzione pragmatica del legacy, non un modello identity/org gia' pulito e completo.

### 4. Il perimetro organization non e' esplicito come aggregate unico
- Nel legacy il dealer e' spesso sia soggetto commerciale sia contesto operativo corrente.
- L'appartenenza organizzativa viene inferita da:
  - dealer corrente in sessione
  - campi come `id_dealer`
  - relazioni operative verso dealer
- Non emerge un boundary pulito tipo `organization -> membership -> scope`.

### 5. Figure operative e assignment esistono gia', ma in forma distribuita
- Esiste una tabella `commerciali` come entita' dedicata.
- Esistono assignment dedicati verso dealer:
  - `dealer_commerciale_dealer`
  - `dealer_operatore_figura`
- `Dealer_operatore_figura_model` introduce figure operative esplicite almeno per:
  - `commerciale`
  - `account`
- `Main_dealer` usa helper runtime e stato di sessione per derivare:
  - figura operativa
  - dealer assegnati
  - dealer attivo
  - restrizioni operative

### 6. Ruolo, figura, membership e assignment sono parzialmente confusi
- In diversi punti il legacy legge il ruolo direttamente da `user_logged->ruolo`.
- In altri punti la figura operativa viene normalizzata tramite helper runtime.
- Alcune responsabilita' sono modellate come assignment di dealer, altre come semantica del ruolo, altre ancora come stato di sessione.
- Questo conferma che il legacy contiene comportamento reale utile, ma non un modello architetturale da copiare.

## Confronto con il target model Neo

### Conferme
- Il target model Neo che separa `account`, `membership`, `assignment` e `capability` e' coerente con i problemi osservati nel legacy.
- Il target model Neo che separa `organization`, `organization unit/scope` e `assignment` e' coerente con la dispersione attuale del concetto di dealer/perimetro operativo.
- La regola backend-first e capability-based resta corretta: il legacy mostra chiaramente il rischio di derivare comportamento da sessione o hardcode UI.

### Correzioni da mantenere
- Neo non dovrebbe usare `users` come surrogate di organization.
- Neo non dovrebbe trattare `dealer`, `admin` e `api dealer` come identita' tecnicamente separate nel modello target.
- Neo non dovrebbe usare il solo ruolo come fonte di verita' per visibilita' e responsabilita' operative.

## Strategia di transizione raccomandata
- Introdurre in Neo un `account` unico autenticabile come base tecnica comune.
- Modellare l'appartenenza organizzativa tramite membership esplicite, senza schiacciare tutto sul record account.
- Modellare gli assignment operativi come relazioni dedicate e contestuali, invece di delegarli a sessione o helper runtime.
- Usare capability backend derivate da:
  - account
  - membership attiva
  - ruolo scoped
  - assignment
  - policy backend
- Trattare l'ACL recente del legacy come fonte di indizi operativi e naming permissionale, non come schema finale da migrare uno-a-uno.

## Rischi principali emersi
- Copiare nel nuovo portale la distinzione tecnica tra ingressi `admin`, `dealer` e API come se fosse una verita' di dominio.
- Portare nel nuovo modello ruoli storici troppo semantici o dipendenti da sessione.
- Modellare dealer come semplice attributo utente invece che come parte del boundary organizzativo corretto.
- Trasformare assignment temporanei del legacy in struttura definitiva senza ulteriore design shared.

## Cosa resta volutamente fuori da questo audit
- Mapping completo dei permessi legacy modulo per modulo.
- Catalogo legacy e gerarchie fornitore/compagnia/prodotto.
- Disegno finale della sidebar capability-based.
- Implementazione persistence del foundation module auth/org nel nuovo portale.

## Risultato finale
`MSN-AUD-001` puo' considerarsi consolidato a livello sufficiente per il bootstrap Neo:
- il legacy fornisce baseline funzionale reale;
- il target model Neo su identity e organization risulta confermato nella direzione;
- il prossimo passo sensato non e' copiare il legacy, ma tradurre questi esiti in governance shared di capability, scope e contratti backend.

## Step successivo consigliato
Aprire `MSN-SHARED-001` con focus su:
- capability model condiviso
- payload target di `/api/v1/me`
- contract iniziale per sidebar/navigation backend-driven
- relazione tra ruolo scoped, membership attiva e assignment contestuali
