<?php

namespace App\Application\Auth\LegacyImport;

use App\Application\Auth\LegacyImport\Contracts\AuthOrgLegacyCandidateMatcher;
use App\Models\DealerOperatorAssignment;
use App\Models\LegacyEntityMapping;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;

class DefaultAuthOrgLegacyCandidateMatcher implements AuthOrgLegacyCandidateMatcher
{
    public function __construct(
        private readonly DefaultAuthOrgLegacyMatchingHeuristics $heuristics,
        private readonly DefaultAuthOrgLegacyMatchingEdgeCases $edgeCases,
        private readonly DefaultAuthOrgLegacyMatchingConfidence $confidence,
    ) {
    }

    public function match(array $resolvedCandidate): array
    {
        $candidateType = (string) ($resolvedCandidate['candidate_type'] ?? 'unknown');
        $resolutionStatus = (string) ($resolvedCandidate['resolution_status'] ?? 'unresolved');

        if ($resolutionStatus !== 'mapped_candidate') {
            return [
                'match_status' => 'not_applicable',
                'match_reason' => 'candidate_is_not_resolved_for_matching',
                'match_strategy' => 'not_applicable',
                'match_confidence' => $this->confidence->levels()[3],
            ];
        }

        $match = match ($candidateType) {
            'user' => $this->matchUser($resolvedCandidate),
            'organization' => $this->matchOrganization($resolvedCandidate),
            'membership' => $this->matchMembership($resolvedCandidate),
            'assignment' => $this->matchAssignment($resolvedCandidate),
            default => [
                'match_status' => 'not_applicable',
                'match_reason' => 'candidate_type_is_not_matchable_yet',
                'match_strategy' => 'not_applicable',
            ],
        };

        $match['match_confidence'] = $this->resolveConfidence($resolvedCandidate, $match);

        return $match;
    }

    /**
     * @param  array<string, mixed>  $resolvedCandidate
     * @return array{match_status: string, match_reason: string, match_strategy: string}
     */
    private function matchUser(array $resolvedCandidate): array
    {
        $email = $this->normalizedEmail($resolvedCandidate['legacy_email'] ?? null);

        if ($email !== '' && User::query()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
            return [
                'match_status' => 'matched_existing_target',
                'match_reason' => 'matched_existing_user_by_normalized_email',
                'match_strategy' => $this->heuristics->userSignals()[0],
            ];
        }

        if ($this->hasLegacyMapping($resolvedCandidate, User::class)) {
            return [
                'match_status' => 'matched_existing_target',
                'match_reason' => 'matched_existing_user_by_legacy_mapping',
                'match_strategy' => $this->heuristics->userSignals()[1],
            ];
        }

        if ($this->isDealerAdminSpecialCase($resolvedCandidate)) {
            return [
                'match_status' => 'ambiguous_match',
                'match_reason' => 'dealer_admin_requires_manual_identity_resolution',
                'match_strategy' => $this->edgeCases->weakSignals()[1],
            ];
        }

        if ($this->hasDealerScopedUsernameSignal($resolvedCandidate)) {
            return [
                'match_status' => 'no_existing_match',
                'match_reason' => 'dealer_scoped_user_candidate_ready_for_create_by_username',
                'match_strategy' => $this->heuristics->userSignals()[3],
            ];
        }

        if ($this->looksLikePlaceholderIdentity($resolvedCandidate['legacy_email'] ?? null)) {
            return [
                'match_status' => 'ambiguous_match',
                'match_reason' => 'user_candidate_uses_placeholder_identity_signal',
                'match_strategy' => $this->edgeCases->weakSignals()[4],
            ];
        }

        if ($email !== '') {
            return [
                'match_status' => 'no_existing_match',
                'match_reason' => 'no_existing_user_match_found',
                'match_strategy' => 'none',
            ];
        }

        return [
            'match_status' => 'ambiguous_match',
            'match_reason' => 'user_candidate_has_insufficient_signals',
            'match_strategy' => $this->edgeCases->stopConditions()[2],
        ];
    }

    /**
     * @param  array<string, mixed>  $resolvedCandidate
     * @return array{match_status: string, match_reason: string, match_strategy: string}
     */
    private function matchOrganization(array $resolvedCandidate): array
    {
        $code = strtolower((string) ($resolvedCandidate['legacy_code'] ?? ''));

        if ($code !== '' && Organization::query()->whereRaw('LOWER(code) = ?', [$code])->exists()) {
            return [
                'match_status' => 'matched_existing_target',
                'match_reason' => 'matched_existing_organization_by_normalized_code',
                'match_strategy' => $this->heuristics->organizationSignals()[0],
            ];
        }

        if ($this->hasLegacyMapping($resolvedCandidate, Organization::class)) {
            return [
                'match_status' => 'matched_existing_target',
                'match_reason' => 'matched_existing_organization_by_legacy_mapping',
                'match_strategy' => $this->heuristics->organizationSignals()[1],
            ];
        }

        $name = trim((string) ($resolvedCandidate['legacy_name'] ?? ''));

        if ($name !== '' && Organization::query()->whereRaw('LOWER(name) = ?', [strtolower($name)])->exists()) {
            return [
                'match_status' => 'ambiguous_match',
                'match_reason' => 'organization_name_is_only_a_weak_signal',
                'match_strategy' => $this->edgeCases->weakSignals()[0],
            ];
        }

        return [
            'match_status' => 'no_existing_match',
            'match_reason' => 'no_existing_organization_match_found',
            'match_strategy' => 'none',
        ];
    }

    /**
     * @param  array<string, mixed>  $resolvedCandidate
     * @return array{match_status: string, match_reason: string, match_strategy: string}
     */
    private function matchMembership(array $resolvedCandidate): array
    {
        $userEmail = $this->normalizedEmail($resolvedCandidate['legacy_user_email'] ?? null);
        $organizationCode = strtolower((string) ($resolvedCandidate['legacy_organization_code'] ?? ''));

        if ($userEmail !== '' && $organizationCode !== '') {
            $membershipExists = OrganizationMembership::query()
                ->whereHas('user', fn ($query) => $query->whereRaw('LOWER(email) = ?', [$userEmail]))
                ->whereHas('organization', fn ($query) => $query->whereRaw('LOWER(code) = ?', [$organizationCode]))
                ->exists();

            if ($membershipExists) {
                return [
                    'match_status' => 'matched_existing_target',
                    'match_reason' => 'matched_existing_membership_by_user_organization_pair',
                    'match_strategy' => $this->heuristics->membershipSignals()[0],
                ];
            }
        }

        if ($this->hasLegacyMapping($resolvedCandidate, OrganizationMembership::class)) {
            return [
                'match_status' => 'matched_existing_target',
                'match_reason' => 'matched_existing_membership_by_legacy_mapping',
                'match_strategy' => $this->heuristics->membershipSignals()[1],
            ];
        }

        if ($this->hasDealerScopedMembershipSignal($resolvedCandidate)) {
            return [
                'match_status' => 'no_existing_match',
                'match_reason' => 'membership_candidate_ready_for_create_by_dealer_scoped_identity',
                'match_strategy' => $this->heuristics->membershipSignals()[3],
            ];
        }

        if (($userEmail === '' && $organizationCode !== '') || ($userEmail !== '' && $organizationCode === '')) {
            return [
                'match_status' => 'ambiguous_match',
                'match_reason' => 'membership_candidate_has_incomplete_pair_signal',
                'match_strategy' => $this->edgeCases->stopConditions()[2],
            ];
        }

        if ($userEmail !== '' && $organizationCode !== '') {
            return [
                'match_status' => 'no_existing_match',
                'match_reason' => 'no_existing_membership_match_found',
                'match_strategy' => 'none',
            ];
        }

        return [
            'match_status' => 'ambiguous_match',
            'match_reason' => 'membership_candidate_has_insufficient_identity_pairing',
            'match_strategy' => $this->edgeCases->stopConditions()[2],
        ];
    }

    /**
     * @param  array<string, mixed>  $resolvedCandidate
     * @return array{match_status: string, match_reason: string, match_strategy: string}
     */
    private function matchAssignment(array $resolvedCandidate): array
    {
        $userEmail = $this->normalizedEmail($resolvedCandidate['legacy_user_email'] ?? null);
        $dealerCode = strtolower((string) ($resolvedCandidate['legacy_dealer_code'] ?? ''));
        $assignmentRoleCode = strtolower((string) ($resolvedCandidate['legacy_assignment_role_code'] ?? 'dealer_operator'));

        if ($userEmail === '' || $dealerCode === '') {
            return [
                'match_status' => 'ambiguous_match',
                'match_reason' => 'assignment_candidate_has_incomplete_operator_dealer_signal',
                'match_strategy' => $this->edgeCases->stopConditions()[2],
            ];
        }

        $assignmentExists = DealerOperatorAssignment::query()
            ->where('assignment_role_code', $assignmentRoleCode)
            ->whereHas('operatorMembership.user', fn ($query) => $query->whereRaw('LOWER(email) = ?', [$userEmail]))
            ->whereHas('dealerOrganization', fn ($query) => $query->whereRaw('LOWER(code) = ?', [$dealerCode]))
            ->exists();

        if ($assignmentExists) {
            return [
                'match_status' => 'matched_existing_target',
                'match_reason' => 'matched_existing_assignment_by_operator_dealer_role',
                'match_strategy' => $this->heuristics->assignmentSignals()[0],
            ];
        }

        if ($this->hasLegacyMapping($resolvedCandidate, DealerOperatorAssignment::class)) {
            return [
                'match_status' => 'matched_existing_target',
                'match_reason' => 'matched_existing_assignment_by_legacy_mapping',
                'match_strategy' => $this->heuristics->assignmentSignals()[1],
            ];
        }

        return [
            'match_status' => 'no_existing_match',
            'match_reason' => 'no_existing_assignment_match_found',
            'match_strategy' => 'none',
        ];
    }

    /**
     * @param  array<string, mixed>  $resolvedCandidate
     */
    private function hasLegacyMapping(array $resolvedCandidate, string $targetModel): bool
    {
        $legacyTable = (string) ($resolvedCandidate['legacy_table'] ?? '');
        $legacyId = $resolvedCandidate['legacy_id'] ?? null;
        $legacyKey = $resolvedCandidate['legacy_key'] ?? null;

        $targetInstance = new $targetModel();

        $query = LegacyEntityMapping::query()
            ->where('legacy_table', $legacyTable)
            ->where('target_type', $targetInstance->getMorphClass());

        if ($legacyTable === '') {
            return false;
        }

        if (is_array($legacyKey) && $legacyKey !== []) {
            return (clone $query)
                ->where('legacy_key_hash', LegacyEntityMapping::makeLegacyKeyHash($legacyKey))
                ->exists();
        }

        if ($legacyId !== null && $legacyId !== '') {
            return (clone $query)
                ->where('legacy_id', (string) $legacyId)
                ->exists();
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $resolvedCandidate
     * @param  array<string, string>  $match
     */
    private function resolveConfidence(array $resolvedCandidate, array $match): string
    {
        $matchStatus = (string) ($match['match_status'] ?? 'not_applicable');
        $matchStrategy = (string) ($match['match_strategy'] ?? 'not_applicable');
        $resolutionStatus = (string) ($resolvedCandidate['resolution_status'] ?? 'unresolved');

        if (
            in_array($matchStatus, ['not_applicable', 'no_existing_match'], true)
            || in_array($resolutionStatus, ['collision', 'unresolved', 'needs_review'], true)
            || in_array($matchStrategy, $this->confidence->downgradeConditions(), true)
        ) {
            return $this->confidence->levels()[3];
        }

        if ($matchStatus === 'ambiguous_match') {
            return $this->confidence->levels()[2];
        }

        if (in_array($matchStrategy, $this->confidence->strongSignals(), true)) {
            return $this->confidence->levels()[0];
        }

        if (in_array($matchStrategy, $this->confidence->mediumSignals(), true)) {
            return $this->confidence->levels()[1];
        }

        if (in_array($matchStrategy, $this->confidence->weakSignals(), true)) {
            return $this->confidence->levels()[2];
        }

        return $this->confidence->levels()[3];
    }

    private function normalizedEmail(mixed $value): string
    {
        $email = strtolower(trim((string) $value));

        if ($email === '' || $this->looksLikePlaceholderIdentity($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return '';
        }

        return $email;
    }

    private function normalizedCode(mixed $value): string
    {
        return strtolower(trim((string) $value));
    }

    private function normalizedUsername(mixed $value): string
    {
        $username = strtolower(trim((string) $value));

        if ($username === '' || $this->looksLikePlaceholderIdentity($username)) {
            return '';
        }

        return $username;
    }

    private function looksLikePlaceholderIdentity(mixed $value): bool
    {
        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['', '-', '--', 'null', 'n/a', 'na', 'nessuna', 'nessuno'], true);
    }

    /**
     * @param  array<string, mixed>  $resolvedCandidate
     */
    private function hasDealerScopedUsernameSignal(array $resolvedCandidate): bool
    {
        if ((string) ($resolvedCandidate['legacy_table'] ?? '') !== 'dealer_collaboratore') {
            return false;
        }

        return $this->normalizedCode($resolvedCandidate['legacy_dealer_code'] ?? null) !== ''
            && $this->normalizedUsername($resolvedCandidate['legacy_username'] ?? null) !== ''
            && in_array((string) ($resolvedCandidate['target_membership_role_code'] ?? ''), ['dealer_seller', 'dealer_admin'], true);
    }

    /**
     * @param  array<string, mixed>  $resolvedCandidate
     */
    private function hasDealerScopedMembershipSignal(array $resolvedCandidate): bool
    {
        if ((string) ($resolvedCandidate['legacy_table'] ?? '') !== 'dealer_collaboratore') {
            return false;
        }

        return $this->normalizedCode($resolvedCandidate['legacy_organization_code'] ?? null) !== ''
            && $this->normalizedUsername($resolvedCandidate['legacy_user_username'] ?? $resolvedCandidate['legacy_username'] ?? null) !== ''
            && in_array((string) ($resolvedCandidate['target_membership_role_code'] ?? ''), ['dealer_seller', 'dealer_admin'], true);
    }

    /**
     * @param  array<string, mixed>  $resolvedCandidate
     */
    private function isDealerAdminSpecialCase(array $resolvedCandidate): bool
    {
        return (string) ($resolvedCandidate['legacy_table'] ?? '') === 'dealer'
            && (string) ($resolvedCandidate['candidate_type'] ?? '') === 'user'
            && (string) ($resolvedCandidate['legacy_channel'] ?? '') === 'dealer_admin'
            && $this->normalizedEmail($resolvedCandidate['legacy_email'] ?? null) === '';
    }
}
