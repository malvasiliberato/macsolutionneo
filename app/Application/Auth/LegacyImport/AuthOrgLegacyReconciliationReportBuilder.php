<?php

namespace App\Application\Auth\LegacyImport;

use Illuminate\Support\Collection;

class AuthOrgLegacyReconciliationReportBuilder
{
    /**
     * @param  Collection<int, array<string, mixed>>  $resolvedRows
     * @return Collection<int, array<string, mixed>>
     */
    public function classifyRows(Collection $resolvedRows): Collection
    {
        return $this->applyCreateOnlyDependencyGuards(
            $resolvedRows
            ->map(fn (array $row) => array_merge($row, [
                'reconciliation_status' => $this->classifyRow($row),
            ]))
            ->values()
        );
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $resolvedRows
     * @return array{
     *     summary: array<string, scalar>,
     *     reconciliation: array<string, scalar|array<int, string>>
     * }
     */
    public function build(
        Collection $resolvedRows,
        string $sourceSystem,
        string $legacyTable,
        string $dataset,
        int $batch,
        bool $dryRun,
    ): array {
        $classifiedRows = $this->classifyRows($resolvedRows);

        $manualReviewRows = $classifiedRows
            ->where('reconciliation_status', 'needs_review')
            ->values();

        $entityBreakdown = [];
        foreach (['user', 'organization', 'membership', 'assignment'] as $entity) {
            $entityRows = $classifiedRows->where('candidate_type', $entity);

            $entityBreakdown[$entity] = [
                'ready_link' => $entityRows->where('reconciliation_status', 'ready_link')->count(),
                'ready_create' => $entityRows->where('reconciliation_status', 'ready_create')->count(),
                'needs_review' => $entityRows->where('reconciliation_status', 'needs_review')->count(),
                'blocked' => $entityRows->where('reconciliation_status', 'blocked')->count(),
            ];
        }

        $laneBreakdown = [];
        foreach (['internal_platform_principal', 'dealer_organization', 'dealer_seller', 'dealer_operator', 'unknown'] as $lane) {
            $laneRows = $classifiedRows->where('candidate_lane', $lane);

            if ($laneRows->isEmpty()) {
                continue;
            }

            $laneBreakdown[$lane] = [
                'ready_link' => $laneRows->where('reconciliation_status', 'ready_link')->count(),
                'ready_create' => $laneRows->where('reconciliation_status', 'ready_create')->count(),
                'needs_review' => $laneRows->where('reconciliation_status', 'needs_review')->count(),
                'blocked' => $laneRows->where('reconciliation_status', 'blocked')->count(),
            ];
        }

        $laneSubtypeBreakdown = [];
        $classifiedRows
            ->filter(fn (array $row) => ! empty($row['candidate_lane_subtype']))
            ->groupBy(fn (array $row) => (string) $row['candidate_lane'])
            ->each(function (Collection $laneRows, string $lane) use (&$laneSubtypeBreakdown) {
                $laneSubtypeBreakdown[$lane] = $laneRows
                    ->groupBy(fn (array $row) => (string) $row['candidate_lane_subtype'])
                    ->map(function (Collection $subtypeRows) {
                        return [
                            'ready_link' => $subtypeRows->where('reconciliation_status', 'ready_link')->count(),
                            'ready_create' => $subtypeRows->where('reconciliation_status', 'ready_create')->count(),
                            'needs_review' => $subtypeRows->where('reconciliation_status', 'needs_review')->count(),
                            'blocked' => $subtypeRows->where('reconciliation_status', 'blocked')->count(),
                        ];
                    })
                    ->sortKeys()
                    ->all();
            });

        $internalPrincipalCategoryBreakdown = $classifiedRows
            ->where('candidate_lane', 'internal_platform_principal')
            ->filter(fn (array $row) => ! empty($row['candidate_lane_subtype']))
            ->groupBy(fn (array $row) => $this->resolveInternalPrincipalCategory((string) $row['candidate_lane_subtype']))
            ->map(function (Collection $categoryRows) {
                return [
                    'ready_link' => $categoryRows->where('reconciliation_status', 'ready_link')->count(),
                    'ready_create' => $categoryRows->where('reconciliation_status', 'ready_create')->count(),
                    'needs_review' => $categoryRows->where('reconciliation_status', 'needs_review')->count(),
                    'blocked' => $categoryRows->where('reconciliation_status', 'blocked')->count(),
                ];
            })
            ->sortKeys()
            ->all();

        $internalPrincipalReviewActionBreakdown = $classifiedRows
            ->where('candidate_lane', 'internal_platform_principal')
            ->filter(fn (array $row) => ! empty($row['candidate_lane_subtype']))
            ->groupBy(fn (array $row) => $this->resolveInternalPrincipalReviewAction((string) $row['candidate_lane_subtype']))
            ->map(function (Collection $actionRows) {
                return [
                    'ready_link' => $actionRows->where('reconciliation_status', 'ready_link')->count(),
                    'ready_create' => $actionRows->where('reconciliation_status', 'ready_create')->count(),
                    'needs_review' => $actionRows->where('reconciliation_status', 'needs_review')->count(),
                    'blocked' => $actionRows->where('reconciliation_status', 'blocked')->count(),
                ];
            })
            ->sortKeys()
            ->all();

        return [
            'summary' => [
                'source_system' => $sourceSystem,
                'legacy_table' => $legacyTable,
                'dataset' => $dataset,
                'batch' => $batch,
                'dry_run' => $dryRun,
                'records_read' => $resolvedRows->count(),
                'user_candidates' => $resolvedRows->where('candidate_type', 'user')->count(),
                'organization_candidates' => $resolvedRows->where('candidate_type', 'organization')->count(),
                'membership_candidates' => $resolvedRows->where('candidate_type', 'membership')->count(),
                'assignment_candidates' => $resolvedRows->where('candidate_type', 'assignment')->count(),
                'mappings_created' => 0,
                'mappings_updated' => 0,
                'collisions' => $resolvedRows->where('resolution_status', 'collision')->count(),
                'unresolved' => $resolvedRows->where('resolution_status', 'unresolved')->count(),
                'needs_review' => $resolvedRows->where('resolution_status', 'needs_review')->count(),
                'resolved_candidates' => $resolvedRows->where('resolution_status', 'mapped_candidate')->count(),
                'matched_existing_targets' => $resolvedRows->where('match_status', 'matched_existing_target')->count(),
                'unmatched_candidates' => $resolvedRows->where('match_status', 'no_existing_match')->count(),
                'ambiguous_matches' => $resolvedRows->where('match_status', 'ambiguous_match')->count(),
                'heuristic_matches' => $resolvedRows->whereIn('match_strategy', [
                    'normalized_email',
                    'normalized_code',
                    'user_plus_organization_pair',
                    'operator_plus_dealer_role',
                ])->count(),
                'mapping_matches' => $resolvedRows->where('match_strategy', 'legacy_mapping')->count(),
                'high_confidence_matches' => $resolvedRows->where('match_confidence', 'high')->count(),
                'medium_confidence_matches' => $resolvedRows->where('match_confidence', 'medium')->count(),
                'low_confidence_matches' => $resolvedRows->where('match_confidence', 'low')->count(),
                'no_confidence_matches' => $resolvedRows->where('match_confidence', 'none')->count(),
                'batch_result' => $resolvedRows->contains(fn (array $row) => in_array($row['resolution_status'], ['collision', 'unresolved'], true))
                    || $resolvedRows->contains(fn (array $row) => $row['match_status'] === 'ambiguous_match')
                    ? 'needs_reconciliation'
                    : 'dry_run_ok',
            ],
            'reconciliation' => [
                'sources_used' => $resolvedRows
                    ->pluck('legacy_table')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all(),
                'matching_keys' => [
                    'user:legacy_email',
                    'organization:legacy_code',
                    'membership:legacy_user_email+legacy_organization_code',
                    'assignment:legacy_user_email+legacy_dealer_code+legacy_assignment_role_code',
                ],
                'auto_match_candidates' => $resolvedRows
                    ->where('match_status', 'matched_existing_target')
                    ->whereIn('match_confidence', ['high', 'medium'])
                    ->count(),
                'importable_high_confidence' => $resolvedRows
                    ->where('match_confidence', 'high')
                    ->count(),
                'ready_link_candidates' => $classifiedRows->where('reconciliation_status', 'ready_link')->count(),
                'ready_create_candidates' => $classifiedRows->where('reconciliation_status', 'ready_create')->count(),
                'manual_review_candidates' => $manualReviewRows->count(),
                'blocked_candidates' => $classifiedRows->where('reconciliation_status', 'blocked')->count(),
                'manual_review_reasons' => $manualReviewRows
                    ->map(fn (array $row) => (string) ($row['reconciliation_override_reason'] ?? $row['match_reason'] ?? $row['resolution_reason'] ?? 'needs_review'))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all(),
                'blocking_reasons' => $classifiedRows
                    ->where('reconciliation_status', 'blocked')
                    ->map(fn (array $row) => (string) ($row['match_reason'] ?? $row['resolution_reason'] ?? 'blocked'))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all(),
                'entity_breakdown' => $entityBreakdown,
                'lane_breakdown' => $laneBreakdown,
                'lane_subtype_breakdown' => $laneSubtypeBreakdown,
                'internal_principal_category_breakdown' => $internalPrincipalCategoryBreakdown,
                'internal_principal_review_action_breakdown' => $internalPrincipalReviewActionBreakdown,
                'review_reason_breakdown' => $this->reasonBreakdown($classifiedRows->where('reconciliation_status', 'needs_review')),
                'blocked_reason_breakdown' => $this->reasonBreakdown($classifiedRows->where('reconciliation_status', 'blocked')),
                'commit_mode' => 'not_available',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function classifyRow(array $row): string
    {
        $resolutionStatus = (string) ($row['resolution_status'] ?? 'unresolved');
        $matchStatus = (string) ($row['match_status'] ?? 'not_applicable');
        $matchConfidence = (string) ($row['match_confidence'] ?? 'none');

        if (in_array($resolutionStatus, ['collision', 'unresolved'], true)) {
            return 'blocked';
        }

        if ($matchStatus === 'matched_existing_target' && in_array($matchConfidence, ['high', 'medium'], true)) {
            return 'ready_link';
        }

        if ($matchStatus === 'no_existing_match' && $this->hasRequiredCreationSignals($row)) {
            return 'ready_create';
        }

        if (in_array($matchStatus, ['ambiguous_match', 'not_applicable'], true) || $resolutionStatus === 'needs_review') {
            return 'needs_review';
        }

        return 'blocked';
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $classifiedRows
     * @return Collection<int, array<string, mixed>>
     */
    private function applyCreateOnlyDependencyGuards(Collection $classifiedRows): Collection
    {
        return $classifiedRows
            ->map(function (array $row) use ($classifiedRows) {
                if (($row['reconciliation_status'] ?? null) !== 'ready_create') {
                    return $row;
                }

                return match ((string) ($row['candidate_type'] ?? 'unknown')) {
                    'membership' => $this->guardMembershipDependencies($row, $classifiedRows),
                    'assignment' => $this->guardAssignmentDependencies($row, $classifiedRows),
                    default => $row,
                };
            })
            ->values();
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function hasRequiredCreationSignals(array $row): bool
    {
        return match ((string) ($row['candidate_type'] ?? 'unknown')) {
            'user' => $this->isValidEmail($row['legacy_email'] ?? null)
                || $this->hasDealerScopedUserCreationSignals($row),
            'organization' => trim((string) ($row['legacy_code'] ?? '')) !== '' && trim((string) ($row['legacy_name'] ?? '')) !== '',
            'membership' => (
                $this->isValidEmail($row['legacy_user_email'] ?? null)
                && trim((string) ($row['legacy_organization_code'] ?? '')) !== ''
            ) || $this->hasDealerScopedMembershipCreationSignals($row),
            'assignment' => $this->isValidEmail($row['legacy_user_email'] ?? null) && trim((string) ($row['legacy_dealer_code'] ?? '')) !== '' && trim((string) ($row['legacy_assignment_role_code'] ?? '')) !== '',
            default => false,
        };
    }

    private function isValidEmail(mixed $value): bool
    {
        $email = trim((string) $value);

        return $email !== '' && ! $this->looksLikePlaceholderIdentity($email) && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $rows
     * @return array<string, int>
     */
    private function reasonBreakdown(Collection $rows): array
    {
        return $rows
            ->map(fn (array $row) => (string) ($row['reconciliation_override_reason'] ?? $row['match_reason'] ?? $row['resolution_reason'] ?? 'unknown'))
            ->filter()
            ->countBy()
            ->sortDesc()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  Collection<int, array<string, mixed>>  $classifiedRows
     * @return array<string, mixed>
     */
    private function guardMembershipDependencies(array $row, Collection $classifiedRows): array
    {
        $hasUserDependency = $this->hasReadyUserDependency($row, $classifiedRows);
        $hasOrganizationDependency = $this->hasReadyOrganizationDependency(
            (string) ($row['legacy_organization_code'] ?? ''),
            $classifiedRows
        );

        if ($hasUserDependency && $hasOrganizationDependency) {
            return $row;
        }

        return array_merge($row, [
            'reconciliation_status' => 'needs_review',
            'reconciliation_override_reason' => match (true) {
                ! $hasUserDependency && ! $hasOrganizationDependency => 'membership_candidate_requires_resolved_user_and_organization_dependencies',
                ! $hasUserDependency => 'membership_candidate_requires_resolved_user_dependency',
                default => 'membership_candidate_requires_resolved_organization_dependency',
            },
        ]);
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  Collection<int, array<string, mixed>>  $classifiedRows
     * @return array<string, mixed>
     */
    private function guardAssignmentDependencies(array $row, Collection $classifiedRows): array
    {
        $hasMembershipDependency = $this->hasReadyMembershipDependency($row, $classifiedRows);
        $hasDealerDependency = $this->hasReadyOrganizationDependency(
            (string) ($row['legacy_dealer_code'] ?? ''),
            $classifiedRows
        );

        if ($hasMembershipDependency && $hasDealerDependency) {
            return $row;
        }

        return array_merge($row, [
            'reconciliation_status' => 'needs_review',
            'reconciliation_override_reason' => match (true) {
                ! $hasMembershipDependency && ! $hasDealerDependency => 'assignment_candidate_requires_resolved_membership_and_dealer_dependencies',
                ! $hasMembershipDependency => 'assignment_candidate_requires_resolved_membership_dependency',
                default => 'assignment_candidate_requires_resolved_dealer_dependency',
            },
        ]);
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function hasDealerScopedUserCreationSignals(array $row): bool
    {
        return (string) ($row['legacy_table'] ?? '') === 'dealer_collaboratore'
            && trim((string) ($row['legacy_dealer_code'] ?? '')) !== ''
            && $this->normalizedUsername($row['legacy_username'] ?? null) !== ''
            && in_array((string) ($row['target_membership_role_code'] ?? ''), ['dealer_seller', 'dealer_admin'], true);
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function hasDealerScopedMembershipCreationSignals(array $row): bool
    {
        return (string) ($row['legacy_table'] ?? '') === 'dealer_collaboratore'
            && trim((string) ($row['legacy_organization_code'] ?? '')) !== ''
            && $this->normalizedUsername($row['legacy_user_username'] ?? $row['legacy_username'] ?? null) !== ''
            && in_array((string) ($row['target_membership_role_code'] ?? ''), ['dealer_seller', 'dealer_admin'], true);
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  Collection<int, array<string, mixed>>  $classifiedRows
     */
    private function hasReadyUserDependency(array $row, Collection $classifiedRows): bool
    {
        $email = strtolower(trim((string) ($row['legacy_user_email'] ?? '')));
        if ($this->isValidEmail($email)) {
            return $classifiedRows->contains(function (array $candidate) use ($email) {
                return ($candidate['candidate_type'] ?? null) === 'user'
                    && in_array($candidate['reconciliation_status'] ?? null, ['ready_create', 'ready_link'], true)
                    && strtolower(trim((string) ($candidate['legacy_email'] ?? ''))) === $email;
            });
        }

        $organizationCode = strtolower(trim((string) ($row['legacy_organization_code'] ?? '')));
        $username = $this->normalizedUsername($row['legacy_user_username'] ?? $row['legacy_username'] ?? null);

        if ($organizationCode === '' || $username === '') {
            return false;
        }

        return $classifiedRows->contains(function (array $candidate) use ($organizationCode, $username) {
            return ($candidate['candidate_type'] ?? null) === 'user'
                && ($candidate['legacy_table'] ?? null) === 'dealer_collaboratore'
                && in_array($candidate['reconciliation_status'] ?? null, ['ready_create', 'ready_link'], true)
                && strtolower(trim((string) ($candidate['legacy_dealer_code'] ?? ''))) === $organizationCode
                && $this->normalizedUsername($candidate['legacy_username'] ?? null) === $username;
        });
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $classifiedRows
     */
    private function hasReadyOrganizationDependency(string $organizationCode, Collection $classifiedRows): bool
    {
        $organizationCode = strtolower(trim($organizationCode));

        if ($organizationCode === '') {
            return false;
        }

        if (\App\Models\Organization::query()->whereRaw('LOWER(code) = ?', [$organizationCode])->exists()) {
            return true;
        }

        return $classifiedRows->contains(function (array $candidate) use ($organizationCode) {
            return ($candidate['candidate_type'] ?? null) === 'organization'
                && in_array($candidate['reconciliation_status'] ?? null, ['ready_create', 'ready_link'], true)
                && strtolower(trim((string) ($candidate['legacy_code'] ?? ''))) === $organizationCode;
        });
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  Collection<int, array<string, mixed>>  $classifiedRows
     */
    private function hasReadyMembershipDependency(array $row, Collection $classifiedRows): bool
    {
        $email = strtolower(trim((string) ($row['legacy_user_email'] ?? '')));
        $dealerCode = strtolower(trim((string) ($row['legacy_dealer_code'] ?? '')));

        if (! $this->isValidEmail($email) || $dealerCode === '') {
            return false;
        }

        if (\App\Models\OrganizationMembership::query()
            ->whereHas('user', fn ($query) => $query->whereRaw('LOWER(email) = ?', [$email]))
            ->whereHas('organization', fn ($query) => $query->whereRaw('LOWER(code) = ?', [$dealerCode]))
            ->exists()) {
            return true;
        }

        return $classifiedRows->contains(function (array $candidate) use ($email, $dealerCode) {
            return ($candidate['candidate_type'] ?? null) === 'membership'
                && in_array($candidate['reconciliation_status'] ?? null, ['ready_create', 'ready_link'], true)
                && strtolower(trim((string) ($candidate['legacy_user_email'] ?? ''))) === $email
                && strtolower(trim((string) ($candidate['legacy_organization_code'] ?? ''))) === $dealerCode;
        });
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

    private function resolveInternalPrincipalCategory(string $subtype): string
    {
        return match ($subtype) {
            'technical_or_test' => 'technical_or_test',
            'corporate_ambiguous' => 'corporate',
            default => 'human_plausible',
        };
    }

    private function resolveInternalPrincipalReviewAction(string $subtype): string
    {
        return match ($subtype) {
            'supporto_tecnico' => 'manual_link_candidate',
            'technical_or_test' => 'do_not_migrate_automatically',
            'corporate_ambiguous' => 'manual_target_decision_required',
            default => 'manual_review_only',
        };
    }
}
