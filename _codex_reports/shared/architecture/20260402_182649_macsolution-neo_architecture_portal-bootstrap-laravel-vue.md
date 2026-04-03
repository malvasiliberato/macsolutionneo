# Portal Bootstrap Laravel Vue

## Contesto
Mac Solution Neo e' il nuovo portale destinato a sostituire progressivamente il legacy CodeIgniter 3. In questo task il focus e' stato creare il bootstrap tecnico reale del progetto, senza implementare ancora moduli business di dominio.

## Obiettivo del task
Costruire una base eseguibile Laravel + Vue, coerente con backend-first / API-first / mobile readiness, con auth foundation, shell UI iniziale, API foundation, migrazioni minime, documentazione persistente e governance allineata.

## Decisioni di bootstrap adottate
- Laravel 10 come base compatibile con il runtime PHP locale disponibile.
- Vue 3 con Inertia per la shell web iniziale del portale.
- Sanctum per auth web/API foundation e readiness per futuri client.
- MySQL locale come base dati di sviluppo e test.
- Velzon usato come base visuale della shell UI, riusando in modo selettivo CSS, font, loghi e pattern di layout.

## Struttura progetto creata
- `app/Http/Controllers/Web/Portal` per dashboard e roadmap iniziali
- `app/Http/Controllers/Api/V1` per endpoint API versionati
- `app/Http/Resources/Api/V1` per serializzazione API
- `app/Application/Portal` per servizi applicativi leggeri di shell/navigation
- `app/Support/Auth` per capability backend condivise
- `config/portal.php` per navigation, bounded context e deferred items
- `resources/js/Layouts`, `resources/js/Pages` per shell e pagine Vue
- `public/vendor/velzon/assets` per gli asset visuali riusati dal template

## Scelta tecnica frontend/backend
- Backend Laravel resta fonte di verita' per auth, ruoli bootstrap, capability e contratti API.
- Frontend Vue resta client del portale, senza business logic di dominio.
- Inertia viene usato per accelerare il bootstrap della shell web senza compromettere la separazione concettuale backend/client.
- Velzon non viene adottato come struttura di progetto: solo come base visuale della shell.

## Strategia auth iniziale
- Login, logout e profilo utente attivi.
- Area riservata protetta da middleware `auth`.
- Utente bootstrap locale seedato: `admin@macsolution.test`.
- Ruolo bootstrap tecnico `platform_admin` introdotto come foundation minima, senza modellare ancora ACL finale di dominio.
- Tracciamento minimale di `last_login_at` e flag `is_active`.

## Strategia web/api iniziale
- Rotte web principali: landing, dashboard, roadmap workspace, profilo.
- Rotte API versionate sotto `/api/v1`.
- Endpoint foundation:
  - `/api/v1/health`
  - `/api/v1/me`
- Navigation shell condivisa dal backend verso Vue via props Inertia, in coerenza con capability-based navigation.

## Strategia database iniziale
- Tabella `users` estesa con `is_active`, `last_login_at`, `softDeletes`.
- Tabelle `roles` e `role_user` introdotte come foundation tecnica minima.
- Nessuna tabella organization o permission di dominio introdotta in questa fase.
- Database locali dedicati:
  - `macsolution_neo`
  - `macsolution_neo_testing`

## Integrazione Velzon
- Sorgente di riferimento: `H:\Velzon_v2.2.0`.
- Variant usata come riferimento: `HTML/dist/creative`.
- Asset riusati in modo selettivo:
  - CSS bootstrap/app/custom/icons
  - font
  - loghi
- Pattern riadattati:
  - `layout-wrapper`
  - topbar
  - sidebar verticale
  - card dashboard
  - page title area

## Cosa e' stato volutamente rimandato
- Organization foundation reale
- ACL finale di dominio con permessi granulari
- Catalog foundation
- Moduli business: preventivi, pratiche, documenti, finanziamenti, firma, notifiche complete
- Sidebar capability-based definitiva derivata dal dominio consolidato

## Rischi e limiti del bootstrap
- La role foundation introdotta e' tecnica e non va confusa con il modello finale identity/org.
- L'uso di Inertia accelera il bootstrap della shell web, ma il perimetro mobile-ready richiedera' consolidamento progressivo dei contratti API.
- Il template Velzon e' riusato solo lato visuale; ulteriori integrazioni dovranno restare selettive per evitare accoppiamenti inutili.
- Il folder `temp_portal_bootstrap` resta nel workspace come artefatto di lavoro creato durante il bootstrap e non fa parte della base applicativa da evolvere.

## Perche' questa base e' coerente con i prossimi step
- Esiste gia' un portale eseguibile localmente, non una bozza teorica.
- Auth, API foundation, shell UI e migrazioni minime permettono di aprire subito il foundation module `Identity + Organization`.
- La distinzione tra bootstrap tecnico, modello target e futura transizione dal legacy resta esplicita.
