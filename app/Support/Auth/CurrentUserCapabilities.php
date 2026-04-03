<?php

namespace App\Support\Auth;

use App\Models\User;

class CurrentUserCapabilities
{
    public function for(User $user, array $context = []): array
    {
        $capabilities = config('portal.base_capabilities', []);
        $roleMap = config('portal.role_capability_map', []);
        $membershipRoleMap = config('portal.membership_role_capability_map', []);
        $assignmentRoleMap = config('portal.assignment_role_capability_map', []);
        $contextCapabilityMap = config('portal.context_capability_map', []);

        foreach ($user->roles as $role) {
            $capabilities = [
                ...$capabilities,
                ...($roleMap[$role->code] ?? []),
            ];
        }

        $membershipRoleCode = trim((string) data_get($context, 'active_membership.role_code', ''));
        if ($membershipRoleCode !== '') {
            $capabilities = [
                ...$capabilities,
                ...($membershipRoleMap[$membershipRoleCode] ?? []),
            ];
        }

        foreach (data_get($context, 'assignments', []) as $assignment) {
            $assignmentRoleCode = trim((string) data_get($assignment, 'assignment_role_code', ''));

            if ($assignmentRoleCode === '') {
                continue;
            }

            $capabilities = [
                ...$capabilities,
                ...($assignmentRoleMap[$assignmentRoleCode] ?? []),
            ];
        }

        if (! empty($context['active_organization'])) {
            $capabilities = [
                ...$capabilities,
                ...($contextCapabilityMap['has_active_organization'] ?? []),
            ];
        }

        if (! empty(data_get($context, 'assignments', []))) {
            $capabilities = [
                ...$capabilities,
                ...($contextCapabilityMap['has_assignments'] ?? []),
            ];
        }

        if ((bool) data_get($context, 'context_switching.can_switch', false)) {
            $capabilities = [
                ...$capabilities,
                ...($contextCapabilityMap['can_switch_context'] ?? []),
            ];
        }

        $capabilities = array_values(array_unique($capabilities));
        sort($capabilities);

        return $capabilities;
    }
}
