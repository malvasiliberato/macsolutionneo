<?php

namespace App\Support\Auth;

use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Http\Request;

class ResolveAuthenticatedPortalContext
{
    public function for(User $user, ?Request $request = null): array
    {
        $memberships = $user->organizationMemberships()
            ->with([
                'organization.parentOrganization',
                'dealerAssignments.dealerOrganization.parentOrganization',
            ])
            ->where('status', 'active')
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get();

        /** @var OrganizationMembership|null $activeMembership */
        $activeMembership = $this->resolveActiveMembership($memberships, $request);
        $activeOrganization = $activeMembership?->organization;
        $selectedMembershipId = $this->resolveSelectedMembershipId($request);
        $activeAssignments = $activeMembership
            ? $activeMembership->dealerAssignments
                ->where('status', 'active')
                ->sortByDesc('is_primary')
                ->values()
            : collect();

        if ($activeMembership && $activeOrganization) {
            return [
                'is_bootstrap' => false,
                'active_organization' => [
                    'id' => $activeOrganization->id,
                    'code' => $activeOrganization->code,
                    'name' => $activeOrganization->name,
                    'type' => $activeOrganization->type,
                    'is_active' => $activeOrganization->is_active,
                    'parent_organization' => $activeOrganization->parentOrganization ? [
                        'id' => $activeOrganization->parentOrganization->id,
                        'code' => $activeOrganization->parentOrganization->code,
                        'name' => $activeOrganization->parentOrganization->name,
                        'type' => $activeOrganization->parentOrganization->type,
                    ] : null,
                ],
                'active_membership' => [
                    'id' => $activeMembership->id,
                    'organization_id' => $activeMembership->organization_id,
                    'role_code' => $activeMembership->role_code,
                    'status' => $activeMembership->status,
                    'is_primary' => $activeMembership->is_primary,
                    'joined_at' => optional($activeMembership->joined_at)?->toIso8601String(),
                ],
                'active_scope' => [
                    'code' => 'organization:' . $activeOrganization->code,
                    'label' => $activeOrganization->name,
                    'is_placeholder' => false,
                ],
                'assignments' => $activeAssignments
                    ->map(fn ($assignment) => [
                        'id' => $assignment->id,
                        'dealer_organization_id' => $assignment->dealerOrganization->id,
                        'dealer_organization' => [
                            'id' => $assignment->dealerOrganization->id,
                            'code' => $assignment->dealerOrganization->code,
                            'name' => $assignment->dealerOrganization->name,
                            'type' => $assignment->dealerOrganization->type,
                            'parent_organization' => $assignment->dealerOrganization->parentOrganization ? [
                                'id' => $assignment->dealerOrganization->parentOrganization->id,
                                'code' => $assignment->dealerOrganization->parentOrganization->code,
                                'name' => $assignment->dealerOrganization->parentOrganization->name,
                            ] : null,
                        ],
                        'assignment_role_code' => $assignment->assignment_role_code,
                        'status' => $assignment->status,
                        'is_primary' => $assignment->is_primary,
                        'assigned_at' => optional($assignment->assigned_at)?->toIso8601String(),
                        'ended_at' => optional($assignment->ended_at)?->toIso8601String(),
                    ])
                    ->values()
                    ->all(),
                'available_memberships' => $memberships
                    ->map(fn (OrganizationMembership $membership) => [
                        'id' => $membership->id,
                        'organization_id' => $membership->organization->id,
                        'organization_code' => $membership->organization->code,
                        'organization_name' => $membership->organization->name,
                        'organization_type' => $membership->organization->type,
                        'parent_organization_code' => $membership->organization->parentOrganization?->code,
                        'role_code' => $membership->role_code,
                        'status' => $membership->status,
                        'is_primary' => $membership->is_primary,
                        'joined_at' => optional($membership->joined_at)?->toIso8601String(),
                    ])
                    ->values()
                    ->all(),
                'available_organizations' => $memberships
                    ->map(fn (OrganizationMembership $membership) => [
                        'id' => $membership->organization->id,
                        'code' => $membership->organization->code,
                        'name' => $membership->organization->name,
                        'type' => $membership->organization->type,
                        'parent_organization_code' => $membership->organization->parentOrganization?->code,
                        'is_primary' => $membership->is_primary,
                    ])
                    ->values()
                    ->all(),
                'context_switching' => [
                    'enabled' => $memberships->count() > 1,
                    'strategy' => $selectedMembershipId ? 'session_selected_membership' : 'primary_membership_first',
                    'can_switch' => $memberships->count() > 1,
                    'selected_membership_id' => $selectedMembershipId,
                ],
            ];
        }

        return [
            'is_bootstrap' => true,
            'active_organization' => null,
            'active_membership' => null,
            'active_scope' => [
                'code' => 'bootstrap',
                'label' => 'Bootstrap workspace context',
                'is_placeholder' => true,
            ],
            'assignments' => [],
            'available_memberships' => [],
            'available_organizations' => [],
            'context_switching' => [
                'enabled' => false,
                'strategy' => 'bootstrap_fallback',
                'can_switch' => false,
                'selected_membership_id' => null,
            ],
        ];
    }

    /**
     * @param \Illuminate\Support\Collection<int, OrganizationMembership> $memberships
     */
    protected function resolveActiveMembership($memberships, ?Request $request = null): ?OrganizationMembership
    {
        $selectedMembershipId = $this->resolveSelectedMembershipId($request);
        if ($selectedMembershipId) {
            $selectedMembership = $memberships->first(function (OrganizationMembership $membership) use ($selectedMembershipId) {
                return $membership->id === $selectedMembershipId;
            });

            if ($selectedMembership) {
                return $selectedMembership;
            }
        }

        return $memberships->first(function (OrganizationMembership $membership) {
            return $membership->is_primary;
        }) ?? $memberships->first();
    }

    protected function resolveSelectedMembershipId(?Request $request = null): ?int
    {
        if (! $request || ! $request->hasSession()) {
            return null;
        }

        $selectedMembershipId = (int) $request->session()->get(
            config('portal.active_membership_session_key', 'portal.active_membership_id'),
            0,
        );

        return $selectedMembershipId > 0 ? $selectedMembershipId : null;
    }
}
