# Contesto
Il filone `legacy -> Neo` per `Identity + Organization` dispone gia' di candidate resolution, matching read-only, euristiche, edge case e confidence design. Manca ancora l'applicazione concreta del confidence scoring dentro il dry-run tecnico.

# Obiettivo del task
Implementare `MSN-AUTH-020` introducendo `match_confidence` e metriche aggregate nel comando `legacy:import:auth-org --dry-run`, senza aprire commit mode o scritture su target Neo.

# Modifiche applicate
- Introdotta implementazione concreta:
  - `app/Application/Auth/LegacyImport/DefaultAuthOrgLegacyMatchingConfidence.php`
- Esteso il matcher auth/org:
  - aggiunge `match_confidence` ai risultati read-only;
  - distingue `high`, `medium`, `low`, `none`.
- Esteso il dry-run auth/org:
  - aggiunge metriche aggregate:
    - `high_confidence_matches`
    - `medium_confidence_matches`
    - `low_confidence_matches`
    - `no_confidence_matches`
- Aggiornati i test console sul comando auth/org.

# Regole applicate nel slice
- `high`
  - mapping tecnico gia' presente;
  - pair membership forte `user + organization`.
- `medium`
  - segnali robusti ma singoli come `normalized_email` o `normalized_code`.
- `low`
  - `ambiguous_match` o segnali deboli.
- `none`
  - `not_applicable`, `no_existing_match`, collisioni, unresolved o casi non affidabili.

# Cosa e' stato volutamente lasciato invariato
- Nessuna scrittura su target Neo
- Nessuna scrittura su `legacy_entity_mappings`
- Nessun commit mode
- Nessun import completo auth/org

# Risultato finale
Il dry-run auth/org ora non dice solo se un match esiste o meno, ma anche quanto e' affidabile in termini tecnici. Questo migliora leggibilita', riconciliazione futura e priorita' operative senza alterare il perimetro read-only.

# Perche' il workspace e' pronto per il prossimo step
Con `match_confidence` disponibile, il prossimo step puo' definire un output tecnico di riconciliazione piu' strutturato per collisioni, ambiguita' e review-required, senza saltare direttamente a import reali.
