# File legacy esaminati piu' rilevanti
- `G:\Mirror\htdocs\macsolution\application\controllers\admin\Login.php`
- `G:\Mirror\htdocs\macsolution\application\controllers\dealer\Login.php`
- `G:\Mirror\htdocs\macsolution\application\libraries\Auth.php`
- `G:\Mirror\htdocs\macsolution\application\libraries\Legacy_admin_acl_bridge.php`
- `G:\Mirror\htdocs\macsolution\application\controllers\admin\Dealer.php`
- `G:\Mirror\htdocs\macsolution\application\controllers\admin\Commerciali.php`
- `G:\Mirror\htdocs\macsolution\application\controllers\admin\Backoffice_users.php`
- `G:\Mirror\htdocs\macsolution\application\helpers\dealer_operatore_runtime_helper.php`
- `G:\Mirror\htdocs\macsolution\application\helpers\dealer_commerciale_runtime_helper.php`
- `G:\Mirror\htdocs\macsolution\application\views\admin\common\3_left_navigation.php`
- `G:\Mirror\htdocs\macsolution\application\controllers\dealer\Dashboard.php`
- `G:\Mirror\htdocs\macsolution\application\controllers\dealer\Pratiche.php`
- `G:\Mirror\htdocs\macsolution\application\controllers\dealer\Preventivi.php`
- `G:\Mirror\htdocs\macsolution\application\helpers\session_helper.php`
- `G:\Mirror\htdocs\macsolution\application\config\acl.php`

## Query / inspection eseguite
- `SHOW TABLES` mirati su `dealer`, `commerciali`, ACL e assignment
- `DESCRIBE` su:
  - `dealer`
  - `dealer_collaboratore`
  - `commerciali`
  - `dealer_commerciale_dealer`
  - `dealer_commerciale_policy`
  - `dealer_operatore_figura`
  - `dealer_operatore_figura_policy`
  - `auth_groups_users`
  - `auth_permissions_users`
- campionamento dati reali su:
  - `dealer.tipo=admin`
  - `dealer.tipo=dealer`
  - `dealer_collaboratore`
  - `commerciali`
  - gruppi ACL degli admin
  - policy/assignment commerciali
- `Select-String` su controller/runtime dealer per verificare uso reale di:
  - `login_dealer`
  - `user_logged`
  - `operatore`
  - `active_dealer`
  - gating su pratiche/preventivi/documenti

## File Neo toccati
- `G:\Mirror\htdocs\macsolutionlaravel\MASTER_PROGRESS.md`

## Impatto runtime
- nullo
- nessuna modifica applicativa
- nessun seed
- nessuna migrazione
- nessun comando import cambiato

## Come usare questo audit nei prossimi task di import
- non leggere piu' `dealer` come sorgente unica di identita'
- usare lane distinti per:
  - internal platform principals
  - dealer organizations
  - seller dealer-scoped
  - operatori `commerciale/account`
- usare ACL e sidebar backoffice solo come indizi del significato degli internal principals, non come schema target da copiare
- usare assignment/policy commerciali per guidare membership/assignment Neo, non per confonderli con i seller dealer-scoped
