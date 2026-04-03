<?php

namespace App\Application\Auth\LegacyImport;

use App\Application\Auth\LegacyImport\Contracts\AuthOrgLegacyCandidateResolver;

class DefaultAuthOrgLegacyCandidateResolver implements AuthOrgLegacyCandidateResolver
{
    public function resolve(array $legacyRow): array
    {
        $candidateType = (string) ($legacyRow['candidate'] ?? 'unknown');
        $status = (string) ($legacyRow['status'] ?? 'unresolved');
        $legacyTable = (string) ($legacyRow['legacy_table'] ?? 'unknown');
        $candidateLane = $this->resolveCandidateLane($legacyRow, $candidateType, $legacyTable);

        $reason = match ($status) {
            'mapped_candidate' => match ($candidateType) {
                'user' => $legacyTable === 'dealer_collaboratore'
                    ? 'legacy_dealer_collaboratore_row_can_be_treated_as_authenticated_dealer_user_candidate'
                    : sprintf('legacy_%s_row_can_be_treated_as_account_candidate', $legacyTable),
                'membership' => $legacyTable === 'dealer_collaboratore'
                    ? 'legacy_dealer_collaboratore_row_can_be_treated_as_dealer_membership_candidate'
                    : sprintf('legacy_%s_row_can_be_treated_as_membership_candidate', $legacyTable),
                'organization' => sprintf('legacy_%s_row_can_be_treated_as_organization_candidate', $legacyTable),
                'assignment' => sprintf('legacy_%s_row_can_be_treated_as_assignment_candidate', $legacyTable),
                default => 'candidate_type_not_supported_yet',
            },
            'collision' => 'legacy_row_points_to_conflicting_candidate_shape',
            'needs_review' => 'legacy_row_requires_manual_review_before_mapping',
            default => 'legacy_row_cannot_be_resolved_automatically',
        };

        return [
            'candidate_type' => in_array($candidateType, ['user', 'organization', 'membership', 'assignment'], true) ? $candidateType : 'unknown',
            'candidate_lane' => $candidateLane['lane'],
            'candidate_lane_subtype' => $candidateLane['subtype'],
            'resolution_status' => in_array($status, ['mapped_candidate', 'needs_review', 'collision', 'unresolved'], true) ? $status : 'unresolved',
            'resolution_reason' => $reason,
        ];
    }

    /**
     * @return array{lane: string, subtype: string|null}
     */
    private function resolveCandidateLane(array $legacyRow, string $candidateType, string $legacyTable): array
    {
        if ($legacyTable === 'dealer' && $candidateType === 'organization') {
            return [
                'lane' => 'dealer_organization',
                'subtype' => null,
            ];
        }

        if ($legacyTable === 'dealer_collaboratore') {
            return [
                'lane' => 'dealer_seller',
                'subtype' => (string) ($legacyRow['target_membership_role_code'] ?? '') ?: null,
            ];
        }

        if ($legacyTable === 'dealer_user_assignment') {
            return [
                'lane' => 'dealer_operator',
                'subtype' => $this->normalizeOperatorSubtype(
                    $legacyRow['legacy_assignment_role_code'] ?? $legacyRow['target_membership_role_code'] ?? null
                ),
            ];
        }

        if ($legacyTable === 'commerciali' || $legacyTable === 'dealer_operatore_figura') {
            return [
                'lane' => 'dealer_operator',
                'subtype' => $this->normalizeOperatorSubtype(
                    $legacyRow['legacy_assignment_role_code'] ?? $legacyRow['target_membership_role_code'] ?? null
                ),
            ];
        }

        if ($legacyTable === 'dealer' && $candidateType === 'user' && (string) ($legacyRow['legacy_channel'] ?? '') === 'dealer_admin') {
            $username = strtolower(trim((string) ($legacyRow['legacy_username'] ?? '')));

            return [
                'lane' => 'internal_platform_principal',
                'subtype' => match (true) {
                    $username === 'admin' => 'superadmin',
                    $username === 'supporto' => 'supporto_tecnico',
                    $username === 'rosy' => 'backoffice_operativo',
                    str_starts_with($username, 'bo_') => 'technical_or_test',
                    $username === 'mac' => 'corporate_ambiguous',
                    default => 'unclassified_internal_principal',
                },
            ];
        }

        return [
            'lane' => 'unknown',
            'subtype' => null,
        ];
    }

    private function normalizeOperatorSubtype(mixed $value): ?string
    {
        $normalized = strtolower(trim((string) $value));

        return match ($normalized) {
            'commerciale', 'dealer_commercial', 'dealer_commerciale' => 'commerciale',
            'account', 'dealer_account', 'dealer_account_manager', 'dealer_operator_member' => 'account',
            default => $normalized !== '' ? $normalized : null,
        };
    }
}
