# Contesto
Nel filone AUTH/ORG di Neo esistono gia' foundation model, reconciliation layer, dry-run reali, signal hardening e commit gate `create-only`. Prima di proseguire con ulteriori correction/import, serve pero' una lettura piu' accurata del legacy come sorgente semantica, non solo come sorgente tecnica di record.

## Obiettivo del task
Chiarire con evidenze reali da DB legacy e codice CI3:
- chi sono davvero gli attori autenticabili;
- quali tabelle contengono autenticabili reali;
- quali relazioni governano accesso, visibilita' e operativita';
- quali strutture legacy NON vanno copiate nel modello Neo.

## Perimetro esplorato
- Tabelle legacy auth/org:
  - `dealer`
  - `dealer_collaboratore`
  - `commerciali`
  - `dealer_commerciale_dealer`
  - `dealer_commerciale_policy`
  - `dealer_operatore_figura`
  - `dealer_operatore_figura_policy`
  - `auth_groups_users`
  - `auth_permissions_users`
  - `user_login`
- Codice CI3 rilevante:
  - login/sessioni admin e dealer
  - runtime operatori `commerciale/account`
  - ACL/grouping backoffice
  - gestione utenti backoffice
  - gestione dealer e collaboratori
  - punti runtime dealer-side che cambiano visibilita' e operativita'

## Tabelle legacy rilevanti
### `dealer`
- Contiene sia principal interni di piattaforma sia organization dealer.
- Il campo forte e' `tipo`:
  - `tipo=admin` => principal interno/backoffice
  - `tipo=dealer` => organization dealer
- Campi con peso runtime forte per `tipo=admin`:
  - `username`
  - `password`
  - `password_fallback`
  - `password_change`
  - `email_pec` per recovery/backoffice users
  - `is_enable`
- Campi con peso runtime forte per `tipo=dealer`:
  - `ragione_sociale`
  - `id_commerciale` come legacy fallback per assegnazione commerciale
  - `flag_*`, `provv_*`, `ivass`, `tipo_dealer` come configurazione dealer/business, non identity pura
- Conclusione semantica:
  - `dealer` non va letto come un actor type unico.

### `dealer_collaboratore`
- E' la sorgente vera dei venditori dealer-scoped autenticabili.
- Campi forti:
  - `id_dealer`
  - `username`
  - `password`
  - `ruolo`
  - `is_enable`
  - `email`
- Ruoli osservati:
  - `collaboratore`
  - `amministratore`
- Semantica:
  - utenti reali della piattaforma dealer-side con credenziali proprie;
  - NON dettagli annidati del dealer.

### `commerciali`
- E' un lane distinto di autenticabili runtime.
- Campi forti:
  - `username`
  - `password`
  - `figura`
  - `is_enable`
  - `email`
- `figura` distingue almeno:
  - `commerciale`
  - `account`
- Semantica:
  - operatori autenticabili che entrano nel portale dealer con contesto portafoglio, non seller dealer-scoped.

### Tabelle di assegnazione/policy
- `dealer_commerciale_dealer`
  - lega un `commerciale` a piu' dealer
- `dealer_commerciale_policy`
  - policy legacy per dealer/commerciale: `manage_pratiche`, `read_only`, blocchi su preventivi/RCA/documenti/finanziamenti
- `dealer_operatore_figura`
  - evoluzione piu' esplicita dell'assegnazione per `commerciale` / `account`
- `dealer_operatore_figura_policy`
  - evoluzione piu' esplicita della policy per figura

### Tabelle ACL backoffice
- `auth_groups_users`
- `auth_permissions_users`
- `auth_permissions_groups`
- `auth_groups`
- `auth_permissions`

Semantica:
- layer autorizzativo del backoffice interno;
- non definisce dealer o seller, ma classifica i principal `dealer.tipo=admin`.

## Punti di codice legacy rilevanti
- [admin/Login.php](G:\Mirror\htdocs\macsolution\application\controllers\admin\Login.php)
- [dealer/Login.php](G:\Mirror\htdocs\macsolution\application\controllers\dealer\Login.php)
- [Auth.php](G:\Mirror\htdocs\macsolution\application\libraries\Auth.php)
- [Legacy_admin_acl_bridge.php](G:\Mirror\htdocs\macsolution\application\libraries\Legacy_admin_acl_bridge.php)
- [Dealer.php](G:\Mirror\htdocs\macsolution\application\controllers\admin\Dealer.php)
- [Commerciali.php](G:\Mirror\htdocs\macsolution\application\controllers\admin\Commerciali.php)
- [Backoffice_users.php](G:\Mirror\htdocs\macsolution\application\controllers\admin\Backoffice_users.php)
- [dealer_operatore_runtime_helper.php](G:\Mirror\htdocs\macsolution\application\helpers\dealer_operatore_runtime_helper.php)
- [dealer_commerciale_runtime_helper.php](G:\Mirror\htdocs\macsolution\application\helpers\dealer_commerciale_runtime_helper.php)
- [3_left_navigation.php](G:\Mirror\htdocs\macsolution\application\views\admin\common\3_left_navigation.php)
- [Dashboard.php](G:\Mirror\htdocs\macsolution\application\controllers\dealer\Dashboard.php)
- [Pratiche.php](G:\Mirror\htdocs\macsolution\application\controllers\dealer\Pratiche.php)
- [Preventivi.php](G:\Mirror\htdocs\macsolution\application\controllers\dealer\Preventivi.php)
- [session_helper.php](G:\Mirror\htdocs\macsolution\application\helpers\session_helper.php)
- [acl.php](G:\Mirror\htdocs\macsolution\application\config\acl.php)

## Attori reali individuati
### 1. Internal platform principals
Sono i record `dealer.tipo=admin`.

Non sono tutti uguali. L'ACL li stratifica almeno in:
- `superadmin`
- `supporto_tecnico`
- `backoffice_admin`
- `backoffice_operatore`
- `backoffice_finanziamenti`
- `backoffice_rca`
- `admin` come gruppo legacy intermedio

Evidenze dati reali:
- `admin` -> gruppo `superadmin`
- `supporto` -> gruppo `supporto_tecnico`
- `rosy` -> gruppo `backoffice_admin`
- molti `bo_*` -> gruppi backoffice/tecnici
- `mac` -> gruppo `admin`

Semantica:
- sono principal interni di piattaforma;
- non sono dealer organization;
- non sono seller dealer-scoped.

### 2. Dealer organizations
Sono i record `dealer.tipo=dealer`.

Semantica:
- soggetti organizzativi/commerciali;
- base del contesto dealer-side;
- ricevono seller, commerciali/account e configurazioni.

### 3. Venditori dealer-scoped
Sono i record `dealer_collaboratore`.

Semantica:
- utenti reali autenticabili del portale;
- appartengono a un dealer via `id_dealer`;
- `ruolo=collaboratore` e `ruolo=amministratore` sono segnali dealer-side, non ruoli platform-wide.

### 4. Commerciale / account
Sono i record `commerciali`.

Semantica:
- operatori autenticabili separati;
- entrano dal login dealer come lane alternativo;
- lavorano su un portafoglio dealer assegnato;
- la loro visibilita' dipende da assignment + policy.

### 5. Actor pseudo-corporate / ambigui
- esempio: `mac`
- semantica non abbastanza pulita per trattarlo automaticamente come user umano o organization.

## Distinzione richiesta
### Internal principals
- `admin` = superadmin con accesso completo e gestione utenti/ACL/backoffice
- `supporto` = principal tecnico personale con accesso ampio e strumenti extra
- `rosy` = backoffice operativo, non superadmin
- `bo_*` = principal tecnici/test/backoffice specializzati

### Dealer
- organization di business

### Venditori dealer-scoped
- utenti dealer-side autenticabili con credenziali proprie

### Commerciale/account
- operatori autenticabili separati, con portafoglio dealer e policy runtime dedicate

## Campi legacy con significato forte
- `dealer.tipo`
- `dealer.username`
- `dealer.password`
- `dealer.password_fallback`
- `dealer.password_change`
- `dealer.email_pec` per internal users
- `dealer.is_enable`
- `dealer_collaboratore.id_dealer`
- `dealer_collaboratore.username`
- `dealer_collaboratore.password`
- `dealer_collaboratore.ruolo`
- `dealer_collaboratore.is_enable`
- `dealer_collaboratore.email`
- `commerciali.username`
- `commerciali.password`
- `commerciali.figura`
- `commerciali.is_enable`
- `dealer_commerciale_dealer.id_commerciale / id_dealer`
- `dealer_commerciale_policy.manage_pratiche / read_only / block_*`
- `dealer_operatore_figura.figura`
- `auth_groups_users.user_type / user_id / group_id`

## Campi legacy deboli o fuorvianti
- `dealer.username` da solo, senza `tipo`
- `dealer.ragione_sociale` nei `tipo=admin`, perche' puo' rappresentare persona, funzione o label corporate
- `dealer_collaboratore.email` quando vuota o placeholder
- `dealer_collaboratore.ruolo` se letto come ruolo globale della piattaforma
- `dealer.id_commerciale` come unico modello di assegnazione, perche' il runtime piu' recente usa anche tabelle dedicate

## Strutture legacy che NON vanno copiate nel Neo
- `dealer` come tabella unica per internal principals e organization dealer
- uso del record `dealer.tipo=admin` come fondazione del modello AUTH/ORG target
- collasso di seller dealer-side e principal interni nello stesso aggregate
- policy/ACL backoffice tradotte 1:1 in ruoli di dominio Neo
- `id_commerciale` su `dealer` come modello finale di assignment
- sessione legacy che miscela `user_logged`, `dealer` e contesto runtime come sorgente di verita'

## Implicazioni concrete per mapping/import AUTH/ORG
### Mapping legacy -> Neo da rafforzare
- `dealer.tipo=admin`
  - NON => `organization`
  - NON => stesso lane dei seller
  - SI' => `internal platform principals` da lane separato
- `dealer.tipo=dealer`
  - SI' => `organizations`
- `dealer_collaboratore`
  - SI' => `users` + `organization_memberships` dealer-scoped
- `commerciali`
  - SI' => `users` + membership/assignment separati dal lane seller

### Reconciliation rules da affinare
- non basta piu' distinguere `admin` vs `dealer`;
- serve distinguere almeno:
  - internal principal
  - seller dealer-scoped
  - commerciale/account operator
  - corporate ambiguous principal

### Trattamento internal users
- i principal interni devono essere letti anche tramite gruppo ACL, non solo tramite tabella `dealer`
- `supporto_tecnico` e `superadmin` vanno tenuti fuori dal lane dealer/seller

### Trattamento venditori
- `dealer_collaboratore` e' confermata come sorgente primaria del lane seller
- `ruolo=amministratore` in `dealer_collaboratore` NON equivale a internal superadmin

### Trattamento dealer
- `dealer.tipo=dealer` resta il candidato organization
- il record `dealer` non va usato per dedurre identita' umane dealer-side se esiste `dealer_collaboratore`

### Trattamento commerciale/account
- `commerciali` va considerata sorgente identitaria reale
- assignment e policy devono orientare membership/assignment Neo, non essere schiacciati nei seller

## Casi ancora ambigui
- `mac`
- principal `dealer.tipo=admin` con label umane ma identita' non abbastanza forte
- eventuali `account` non ancora popolati in modo esteso nel dataset osservato
- overlap storici tra `dealer.id_commerciale` e tabelle piu' recenti di assignment

## Cosa e' stato implementato davvero
- Nessun cambiamento runtime
- Nessuna migrazione
- Nessun nuovo commit import
- Audit reale su DB + codice CI3
- Consolidamento in report e plancia

## Raccomandazione operativa sul prossimo step
Aprire un macro-step di refinement del mapping import `AUTH/ORG` che separi in modo esplicito i lane:
- `internal platform principals`
- `dealer organizations`
- `dealer sellers`
- `commerciale/account operators`

Il prossimo slice non dovrebbe partire da nuove scritture, ma dal riallineamento delle regole di candidate classification e manual reconciliation secondo questa semantica verificata.
