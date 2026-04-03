# File esaminati o toccati
## Legacy esaminati
- `G:\Mirror\htdocs\macsolution\application\config\acl.php`
- `G:\Mirror\htdocs\macsolution\application\views\admin\common\3_left_navigation.php`
- `G:\Mirror\htdocs\macsolution\application\controllers\admin\Backoffice_users.php`
- `G:\Mirror\htdocs\macsolution\application\controllers\admin\Error_monitor.php`
- `G:\Mirror\htdocs\macsolution\application\controllers\admin\Dashboard.php`
- `G:\Mirror\htdocs\macsolution\application\controllers\admin\Pratiche.php`

## Neo aggiornati
- `G:\Mirror\htdocs\macsolutionlaravel\AI_CONTEXT.md`
- `G:\Mirror\htdocs\macsolutionlaravel\MASTER_PROGRESS.md`

# Comandi e inspection usati
- lettura diretta dei file legacy rilevanti
- ricerca testuale mirata su gruppi ACL, sidebar e controller admin/backoffice

# Impatto runtime
- Impatto runtime nullo.
- Nessuna migrazione, nessun seed, nessuna modifica al gate `create-only`.
- Refinement esclusivamente documentale e di governance del lane review.

# Come usare questo refinement nel prossimo passo AUTH/ORG
- Trattare `supporto` come primo caso utile di `manual_link_candidate`.
- Lasciare `admin` e `rosy` nel lane `manual_review_only`.
- Non riaprire commit o merge automatici per questi principal.
- Usare il lane review solo per chiarire target handling e decisioni manuali governate.
