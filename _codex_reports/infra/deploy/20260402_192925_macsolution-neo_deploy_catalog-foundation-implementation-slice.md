# Mac Solution Neo - Deploy Notes Catalog Foundation Implementation Slice

## File toccati
- `app/Models/Supplier.php`
- `app/Models/Company.php`
- `app/Models/Product.php`
- `app/Models/Coverage.php`
- `database/migrations/2026_04_02_192700_create_catalog_foundation_tables.php`
- `database/seeders/DatabaseSeeder.php`
- `tests/Feature/CatalogFoundationTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Impatto setup locale
- Nuova migrazione richiesta.
- Seeder aggiornato con primitive catalogo bootstrap.
- Nessuna modifica richiesta a `.env` o Laragon.

## Comandi locali
- `php artisan migrate`
- `php artisan db:seed`

Per riallineare tutto da zero:
- `php artisan migrate:fresh --seed`

## Come verificare
- `php artisan test`
- controllare che esistano:
  - `suppliers`
  - `companies`
  - `products`
  - `coverages`
  - `coverage_product`

## Attenzione
- Le entita' introdotte sono foundation tecniche minime.
- Non rappresentano ancora pricing, dealer enablement o catalogo completo di dominio.
