# Identity Legacy Mapping Persistence Slice Deploy Notes

## File creati o aggiornati
- `app/Models/LegacyEntityMapping.php`
- `app/Models/User.php`
- `app/Models/Organization.php`
- `app/Models/OrganizationMembership.php`
- `database/migrations/2026_04_02_195430_create_legacy_entity_mappings_table.php`
- `database/seeders/DatabaseSeeder.php`
- `tests/Feature/LegacyImportMappingPersistenceTest.php`
- `AI_CONTEXT.md`
- `MASTER_PROGRESS.md`

## Impatto runtime
- Introdotta solo persistence tecnica backend.
- Nessuna route, pagina o API utente finale modificata.
- Nessun comando artisan nuovo introdotto.

## Verifica consigliata
- eseguire `php artisan migrate`
- eseguire `php artisan db:seed`
- eseguire `php artisan test`

## Uso operativo
- Riutilizzare `legacy_entity_mappings` per tracking tecnico auth/org.
- Non usare ancora questa tabella come sostituto del dominio.
- Introdurre import reali solo dopo il design del contratto minimo di import.
