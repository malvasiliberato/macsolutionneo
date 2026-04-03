<?php

namespace App\Application\Auth\LegacyImport;

use App\Application\Auth\LegacyImport\Contracts\AuthOrgLegacyMatchingConfidence;

class DefaultAuthOrgLegacyMatchingConfidence implements AuthOrgLegacyMatchingConfidence
{
    public function levels(): array
    {
        return [
            'high',
            'medium',
            'low',
            'none',
        ];
    }

    public function strongSignals(): array
    {
        return [
            'legacy_mapping',
            'user_plus_organization_pair',
            'operator_plus_dealer_role',
        ];
    }

    public function mediumSignals(): array
    {
        return [
            'normalized_email',
            'normalized_code',
        ];
    }

    public function weakSignals(): array
    {
        return [
            'normalized_name_only',
            'legacy_role_only',
            'descriptive_text_only',
            'semantically_unconfirmed_join',
            'insufficient_signals',
        ];
    }

    public function downgradeConditions(): array
    {
        return [
            'ambiguous_match',
            'collision',
            'unresolved',
            'needs_review',
            'mapping_conflicts_with_resolved_candidate',
            'multiple_incompatible_strong_matches',
            'inactive_or_semantically_incompatible_target',
        ];
    }
}
