<?php

namespace App\Application\Auth\LegacyImport;

use App\Application\Auth\LegacyImport\Contracts\AuthOrgLegacyMatchingEdgeCases;

class DefaultAuthOrgLegacyMatchingEdgeCases implements AuthOrgLegacyMatchingEdgeCases
{
    public function ambiguousSignals(): array
    {
        return [
            'multiple_targets_for_same_strong_signal',
            'conflicting_strong_signals',
            'legacy_mapping_conflicts_with_heuristic',
        ];
    }

    public function weakSignals(): array
    {
        return [
            'normalized_name_only',
            'legacy_role_only',
            'descriptive_text_only',
            'semantically_unconfirmed_join',
            'placeholder_identity_signal',
        ];
    }

    public function stopConditions(): array
    {
        return [
            'multiple_incompatible_strong_matches',
            'inactive_or_semantically_incompatible_target',
            'insufficient_signals',
            'mapping_conflicts_with_resolved_candidate',
        ];
    }
}
