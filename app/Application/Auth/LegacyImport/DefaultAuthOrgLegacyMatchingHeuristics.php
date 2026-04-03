<?php

namespace App\Application\Auth\LegacyImport;

use App\Application\Auth\LegacyImport\Contracts\AuthOrgLegacyMatchingHeuristics;

class DefaultAuthOrgLegacyMatchingHeuristics implements AuthOrgLegacyMatchingHeuristics
{
    public function userSignals(): array
    {
        return [
            'normalized_email',
            'legacy_mapping',
            'channel_plus_legacy_identifier',
            'dealer_scoped_username',
        ];
    }

    public function organizationSignals(): array
    {
        return [
            'normalized_code',
            'legacy_mapping',
            'normalized_name_weak_signal',
        ];
    }

    public function membershipSignals(): array
    {
        return [
            'user_plus_organization_pair',
            'legacy_mapping',
            'legacy_role_secondary_signal',
            'dealer_scoped_membership_pair',
        ];
    }

    public function assignmentSignals(): array
    {
        return [
            'operator_plus_dealer_role',
            'legacy_mapping',
            'dealer_code_plus_operator_email',
        ];
    }
}
