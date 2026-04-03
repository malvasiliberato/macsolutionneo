<?php

namespace App\Application\Auth\LegacyImport;

use App\Models\DealerOperatorAssignment;
use App\Models\LegacyEntityMapping;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthOrgLegacyCreateOnlyCommitGate
{
    private array $createdUsersByIdentity = [];

    private array $createdOrganizationsByCode = [];

    private array $createdMembershipsByIdentity = [];

    public function __construct(
        private readonly AuthOrgLegacyDryRun $dryRun,
    ) {
    }

    public function run(string $sourceSystem, string $legacyTable, string $dataset, int $batch): array
    {
        $classifiedRows = $this->dryRun->classify($sourceSystem, $legacyTable, $dataset, $batch);

        $readyCreateRows = $classifiedRows->where('reconciliation_status', 'ready_create')->values();
        $reviewRows = $classifiedRows->where('reconciliation_status', 'needs_review')->values();
        $blockedRows = $classifiedRows->where('reconciliation_status', 'blocked')->values();

        $state = [
            'committed_users' => 0,
            'committed_organizations' => 0,
            'committed_memberships' => 0,
            'committed_assignments' => 0,
            'mappings_created' => 0,
            'mappings_updated' => 0,
            'skipped_already_mapped' => 0,
            'skipped_conflicts' => 0,
            'excluded_due_missing_dependencies' => 0,
            'synthetic_emails_assigned' => 0,
            'excluded_reasons' => [],
        ];

        $this->createdUsersByIdentity = [];
        $this->createdOrganizationsByCode = [];
        $this->createdMembershipsByIdentity = [];

        DB::transaction(function () use ($readyCreateRows, $sourceSystem, &$state) {
            foreach (['organization', 'user', 'membership', 'assignment'] as $candidateType) {
                foreach ($readyCreateRows->where('candidate_type', $candidateType)->values() as $row) {
                    $this->commitRow($row, $sourceSystem, $state);
                }
            }
        });

        return [
            'summary' => [
                'source_system' => $sourceSystem,
                'legacy_table' => $legacyTable,
                'dataset' => $dataset,
                'batch' => $batch,
                'gate_mode' => 'create_only',
                'records_scanned' => $classifiedRows->count(),
                'ready_create_candidates' => $readyCreateRows->count(),
                'manual_review_candidates' => $reviewRows->count(),
                'blocked_candidates' => $blockedRows->count(),
                'committed_users' => $state['committed_users'],
                'committed_organizations' => $state['committed_organizations'],
                'committed_memberships' => $state['committed_memberships'],
                'committed_assignments' => $state['committed_assignments'],
                'mappings_created' => $state['mappings_created'],
                'mappings_updated' => $state['mappings_updated'],
                'skipped_already_mapped' => $state['skipped_already_mapped'],
                'skipped_conflicts' => $state['skipped_conflicts'],
                'excluded_due_missing_dependencies' => $state['excluded_due_missing_dependencies'],
                'synthetic_emails_assigned' => $state['synthetic_emails_assigned'],
                'gate_result' => 'create_only_commit_ok',
            ],
            'gate' => [
                'commit_scope' => ['user', 'organization', 'membership', 'assignment'],
                'excluded_review_candidates' => $reviewRows->count(),
                'excluded_blocked_candidates' => $blockedRows->count(),
                'excluded_review_reasons' => $this->reasonBreakdown($reviewRows),
                'excluded_blocked_reasons' => $this->reasonBreakdown($blockedRows),
                'excluded_runtime_reasons' => $state['excluded_reasons'],
            ],
        ];
    }

    private function commitRow(array $row, string $sourceSystem, array &$state): void
    {
        match ((string) ($row['candidate_type'] ?? 'unknown')) {
            'organization' => $this->commitOrganization($row, $sourceSystem, $state),
            'user' => $this->commitUser($row, $sourceSystem, $state),
            'membership' => $this->commitMembership($row, $sourceSystem, $state),
            'assignment' => $this->commitAssignment($row, $sourceSystem, $state),
            default => null,
        };
    }

    private function commitOrganization(array $row, string $sourceSystem, array &$state): void
    {
        if ($this->hasExistingMapping($row, Organization::class)) {
            $state['skipped_already_mapped']++;

            return;
        }

        $code = strtolower(trim((string) ($row['legacy_code'] ?? '')));
        if ($code === '') {
            $this->excludeRuntimeReason($state, 'organization_missing_code');

            return;
        }

        if (Organization::query()->where('code', $code)->exists()) {
            $this->excludeRuntimeReason($state, 'organization_unique_key_conflict');
            $state['skipped_conflicts']++;

            return;
        }

        $organization = Organization::query()->create([
            'parent_organization_id' => null,
            'code' => $code,
            'name' => trim((string) ($row['legacy_name'] ?? $code)),
            'type' => 'dealer',
            'is_active' => true,
        ]);

        $this->persistMapping($row, $sourceSystem, $organization, $state);
        $this->createdOrganizationsByCode[$code] = $organization;
        $state['committed_organizations']++;
    }

    private function commitUser(array $row, string $sourceSystem, array &$state): void
    {
        if ($this->hasExistingMapping($row, User::class)) {
            $state['skipped_already_mapped']++;

            return;
        }

        $email = $this->normalizedEmail($row['legacy_email'] ?? null);
        if ($email === '') {
            $email = $this->syntheticEmail($row);
            $state['synthetic_emails_assigned']++;
        }

        if (User::query()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
            $this->excludeRuntimeReason($state, 'user_unique_key_conflict');
            $state['skipped_conflicts']++;

            return;
        }

        $user = User::query()->create([
            'name' => trim((string) ($row['legacy_name'] ?? $row['legacy_username'] ?? 'Legacy Imported User')),
            'email' => $email,
            'password' => Hash::make(Str::uuid()->toString()),
            'is_active' => true,
        ]);

        $this->persistMapping($row, $sourceSystem, $user, $state);
        $this->createdUsersByIdentity[$this->userIdentityKey($row)] = $user;
        $state['committed_users']++;
    }

    private function commitMembership(array $row, string $sourceSystem, array &$state): void
    {
        if ($this->hasExistingMapping($row, OrganizationMembership::class)) {
            $state['skipped_already_mapped']++;

            return;
        }

        $user = $this->resolveUserForMembership($row);
        $organization = $this->resolveOrganizationForMembership($row);

        if (! $user || ! $organization) {
            $this->excludeRuntimeReason($state, 'membership_missing_user_or_organization_dependency');
            $state['excluded_due_missing_dependencies']++;

            return;
        }

        if (OrganizationMembership::query()
            ->where('user_id', $user->id)
            ->where('organization_id', $organization->id)
            ->exists()) {
            $this->excludeRuntimeReason($state, 'membership_unique_key_conflict');
            $state['skipped_conflicts']++;

            return;
        }

        $membership = OrganizationMembership::query()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'role_code' => (string) ($row['target_membership_role_code'] ?? 'member'),
            'status' => 'active',
            'is_primary' => ! OrganizationMembership::query()->where('user_id', $user->id)->exists(),
            'joined_at' => now(),
        ]);

        $this->persistMapping($row, $sourceSystem, $membership, $state);
        $this->createdMembershipsByIdentity[$this->membershipIdentityKey($row)] = $membership;
        $state['committed_memberships']++;
    }

    private function commitAssignment(array $row, string $sourceSystem, array &$state): void
    {
        if ($this->hasExistingMapping($row, DealerOperatorAssignment::class)) {
            $state['skipped_already_mapped']++;

            return;
        }

        $membership = $this->resolveMembershipForAssignment($row);
        $dealer = $this->resolveDealerForAssignment($row);

        if (! $membership || ! $dealer) {
            $this->excludeRuntimeReason($state, 'assignment_missing_membership_or_dealer_dependency');
            $state['excluded_due_missing_dependencies']++;

            return;
        }

        $roleCode = strtolower(trim((string) ($row['legacy_assignment_role_code'] ?? 'dealer_operator')));

        if (DealerOperatorAssignment::query()
            ->where('dealer_organization_id', $dealer->id)
            ->where('operator_membership_id', $membership->id)
            ->where('assignment_role_code', $roleCode)
            ->exists()) {
            $this->excludeRuntimeReason($state, 'assignment_unique_key_conflict');
            $state['skipped_conflicts']++;

            return;
        }

        $assignment = DealerOperatorAssignment::query()->create([
            'dealer_organization_id' => $dealer->id,
            'operator_membership_id' => $membership->id,
            'assignment_role_code' => $roleCode,
            'status' => 'active',
            'is_primary' => ! DealerOperatorAssignment::query()
                ->where('dealer_organization_id', $dealer->id)
                ->where('operator_membership_id', $membership->id)
                ->exists(),
            'assigned_at' => now(),
            'ended_at' => null,
        ]);

        $this->persistMapping($row, $sourceSystem, $assignment, $state);
        $state['committed_assignments']++;
    }

    private function hasExistingMapping(array $row, string $targetModel): bool
    {
        $legacyTable = (string) ($row['legacy_table'] ?? '');
        $legacyKey = $row['legacy_key'] ?? null;
        $legacyId = $row['legacy_id'] ?? null;

        if ($legacyTable === '') {
            return false;
        }

        $targetInstance = new $targetModel();
        $query = LegacyEntityMapping::query()
            ->where('legacy_table', $legacyTable)
            ->where('target_type', $targetInstance->getMorphClass());

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

    private function persistMapping(array $row, string $sourceSystem, object $target, array &$state): void
    {
        $legacyKey = is_array($row['legacy_key'] ?? null) ? $row['legacy_key'] : [];
        $attributes = [
            'source_system' => $sourceSystem,
            'legacy_table' => (string) ($row['legacy_table'] ?? ''),
            'legacy_key_hash' => LegacyEntityMapping::makeLegacyKeyHash($legacyKey),
        ];

        $mapping = LegacyEntityMapping::query()->firstOrNew($attributes);
        $wasExisting = $mapping->exists;

        $mapping->fill([
            'legacy_id' => $row['legacy_id'] ?? null,
            'legacy_key' => $legacyKey,
            'target_type' => $target->getMorphClass(),
            'target_id' => $target->getKey(),
            'mapping_status' => 'committed_create_only',
            'last_imported_at' => now(),
        ]);
        $mapping->save();

        if ($wasExisting) {
            $state['mappings_updated']++;
        } else {
            $state['mappings_created']++;
        }
    }

    private function syntheticEmail(array $row): string
    {
        $legacyTable = strtolower(trim((string) ($row['legacy_table'] ?? 'legacy')));
        $legacyId = trim((string) ($row['legacy_id'] ?? ''));
        $username = strtolower(trim((string) ($row['legacy_username'] ?? $row['legacy_user_username'] ?? '')));
        $fingerprint = $legacyId !== ''
            ? $legacyId
            : substr(LegacyEntityMapping::makeLegacyKeyHash((array) ($row['legacy_key'] ?? [])), 0, 12);
        $localPart = trim(sprintf(
            'legacy-%s-%s-%s',
            preg_replace('/[^a-z0-9]+/', '-', $legacyTable) ?: 'record',
            preg_replace('/[^a-z0-9]+/', '-', $username) ?: 'user',
            preg_replace('/[^a-z0-9]+/', '-', strtolower($fingerprint)) ?: 'id',
        ), '-');

        return $localPart . '@' . config('legacy_import.auth_org.create_only.synthetic_email_domain');
    }

    private function resolveUserForMembership(array $row): ?User
    {
        $identityKey = $this->membershipUserIdentityKey($row);

        if (isset($this->createdUsersByIdentity[$identityKey])) {
            return $this->createdUsersByIdentity[$identityKey];
        }

        $email = $this->normalizedEmail($row['legacy_user_email'] ?? null);
        if ($email !== '') {
            return User::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();
        }

        $organizationCode = strtolower(trim((string) ($row['legacy_organization_code'] ?? '')));
        $username = strtolower(trim((string) ($row['legacy_user_username'] ?? $row['legacy_username'] ?? '')));

        if ($organizationCode === '' || $username === '') {
            return null;
        }

        return User::query()
            ->whereHas('organizationMemberships.organization', fn ($query) => $query->whereRaw('LOWER(code) = ?', [$organizationCode]))
            ->whereRaw('LOWER(name) = ?', [$username])
            ->first();
    }

    private function resolveOrganizationForMembership(array $row): ?Organization
    {
        $code = strtolower(trim((string) ($row['legacy_organization_code'] ?? '')));

        return $this->createdOrganizationsByCode[$code]
            ?? Organization::query()->where('code', $code)->first();
    }

    private function resolveMembershipForAssignment(array $row): ?OrganizationMembership
    {
        $email = $this->normalizedEmail($row['legacy_user_email'] ?? null);
        $dealerCode = strtolower(trim((string) ($row['legacy_dealer_code'] ?? '')));

        if ($email === '' || $dealerCode === '') {
            return null;
        }

        $key = sprintf('email:%s|org:%s', $email, $dealerCode);

        return $this->createdMembershipsByIdentity[$key]
            ?? OrganizationMembership::query()
                ->whereHas('user', fn ($query) => $query->whereRaw('LOWER(email) = ?', [$email]))
                ->whereHas('organization', fn ($query) => $query->whereRaw('LOWER(code) = ?', [$dealerCode]))
                ->first();
    }

    private function resolveDealerForAssignment(array $row): ?Organization
    {
        $code = strtolower(trim((string) ($row['legacy_dealer_code'] ?? '')));

        return $this->createdOrganizationsByCode[$code]
            ?? Organization::query()->where('code', $code)->first();
    }

    private function userIdentityKey(array $row): string
    {
        $email = $this->normalizedEmail($row['legacy_email'] ?? null);
        if ($email !== '') {
            return 'email:' . $email;
        }

        return sprintf(
            'dealer_user:%s|username:%s',
            strtolower(trim((string) ($row['legacy_dealer_code'] ?? ''))),
            strtolower(trim((string) ($row['legacy_username'] ?? '')))
        );
    }

    private function membershipUserIdentityKey(array $row): string
    {
        $email = $this->normalizedEmail($row['legacy_user_email'] ?? null);
        if ($email !== '') {
            return 'email:' . $email;
        }

        return sprintf(
            'dealer_user:%s|username:%s',
            strtolower(trim((string) ($row['legacy_organization_code'] ?? ''))),
            strtolower(trim((string) ($row['legacy_user_username'] ?? $row['legacy_username'] ?? '')))
        );
    }

    private function membershipIdentityKey(array $row): string
    {
        $email = $this->normalizedEmail($row['legacy_user_email'] ?? null);
        $organizationCode = strtolower(trim((string) ($row['legacy_organization_code'] ?? '')));

        if ($email !== '') {
            return sprintf('email:%s|org:%s', $email, $organizationCode);
        }

        return sprintf(
            'dealer_user:%s|username:%s|org:%s',
            $organizationCode,
            strtolower(trim((string) ($row['legacy_user_username'] ?? $row['legacy_username'] ?? ''))),
            $organizationCode,
        );
    }

    private function excludeRuntimeReason(array &$state, string $reason): void
    {
        $state['excluded_reasons'][$reason] = ($state['excluded_reasons'][$reason] ?? 0) + 1;
    }

    private function normalizedEmail(mixed $value): string
    {
        $email = strtolower(trim((string) $value));

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return '';
        }

        return $email;
    }

    private function reasonBreakdown(Collection $rows): array
    {
        return $rows
            ->map(fn (array $row) => (string) ($row['reconciliation_override_reason'] ?? $row['match_reason'] ?? $row['resolution_reason'] ?? 'unknown'))
            ->filter()
            ->countBy()
            ->sortDesc()
            ->all();
    }
}
