<?php

namespace Tests\Feature\Api;

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MeTest extends TestCase
{
    use DatabaseTransactions;

    protected static bool $schemaIsReady = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (! self::$schemaIsReady) {
            $this->artisan('migrate:fresh');
            self::$schemaIsReady = true;
        }
    }

    public function test_authenticated_users_can_fetch_their_api_profile(): void
    {
        $user = User::factory()->create();
        $role = Role::query()->create([
            'code' => 'platform_admin',
            'name' => 'Platform Admin',
            'is_system' => true,
        ]);
        $organization = Organization::query()->create([
            'code' => 'workspace-test',
            'name' => 'Workspace Test',
            'type' => 'workspace',
            'is_active' => true,
        ]);
        $dealer = Organization::query()->create([
            'parent_organization_id' => $organization->id,
            'code' => 'dealer-alpha',
            'name' => 'Dealer Alpha',
            'type' => 'dealer',
            'is_active' => true,
        ]);

        $user->roles()->attach($role->id, ['assigned_at' => now()]);
        $membership = $user->organizationMemberships()->create([
            'organization_id' => $organization->id,
            'role_code' => 'workspace_admin',
            'status' => 'active',
            'is_primary' => true,
            'joined_at' => now(),
        ]);
        $membership->dealerAssignments()->create([
            'dealer_organization_id' => $dealer->id,
            'assignment_role_code' => 'dealer_account_manager',
            'status' => 'active',
            'is_primary' => true,
            'assigned_at' => now(),
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/me');

        $response
            ->assertOk()
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.roles.0.code', 'platform_admin')
            ->assertJsonPath('data.context.is_bootstrap', false)
            ->assertJsonPath('data.context.active_organization.code', 'workspace-test')
            ->assertJsonPath('data.context.active_organization.parent_organization', null)
            ->assertJsonPath('data.context.active_membership.role_code', 'workspace_admin')
            ->assertJsonPath('data.context.active_scope.code', 'organization:workspace-test')
            ->assertJsonPath('data.context.assignments.0.dealer_organization.code', 'dealer-alpha')
            ->assertJsonPath('data.context.assignments.0.assignment_role_code', 'dealer_account_manager')
            ->assertJsonPath('data.context.available_memberships.0.organization_code', 'workspace-test')
            ->assertJsonPath('data.context.context_switching.enabled', false);

        $this->assertContains('api.me.read', $response->json('data.capabilities'));
        $this->assertContains('organization.context.active', $response->json('data.capabilities'));
        $this->assertContains('organization.context.read', $response->json('data.capabilities'));
        $this->assertContains('dealer.assignment.read', $response->json('data.capabilities'));
        $this->assertContains('dealer.assignment.active', $response->json('data.capabilities'));
    }

    public function test_primary_membership_is_preferred_when_multiple_active_memberships_exist(): void
    {
        $user = User::factory()->create();
        $primaryOrganization = Organization::query()->create([
            'code' => 'workspace-primary',
            'name' => 'Workspace Primary',
            'type' => 'workspace',
            'is_active' => true,
        ]);
        $secondaryOrganization = Organization::query()->create([
            'code' => 'workspace-secondary',
            'name' => 'Workspace Secondary',
            'type' => 'workspace',
            'is_active' => true,
        ]);

        $user->organizationMemberships()->create([
            'organization_id' => $secondaryOrganization->id,
            'role_code' => 'member',
            'status' => 'active',
            'is_primary' => false,
            'joined_at' => now()->subDay(),
        ]);

        $user->organizationMemberships()->create([
            'organization_id' => $primaryOrganization->id,
            'role_code' => 'owner',
            'status' => 'active',
            'is_primary' => true,
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/me');

        $response
            ->assertOk()
            ->assertJsonPath('data.context.active_organization.code', 'workspace-primary')
            ->assertJsonPath('data.context.active_membership.role_code', 'owner')
            ->assertJsonPath('data.context.context_switching.enabled', true)
            ->assertJsonPath('data.context.context_switching.strategy', 'primary_membership_first');

        $this->assertContains('organization.context.active', $response->json('data.capabilities'));
        $this->assertContains('organization.context.manage', $response->json('data.capabilities'));
        $this->assertContains('organization.context.switch', $response->json('data.capabilities'));
    }

    public function test_users_can_switch_their_active_membership(): void
    {
        $user = User::factory()->create();
        $primaryOrganization = Organization::query()->create([
            'code' => 'workspace-primary',
            'name' => 'Workspace Primary',
            'type' => 'workspace',
            'is_active' => true,
        ]);
        $secondaryOrganization = Organization::query()->create([
            'code' => 'workspace-secondary',
            'name' => 'Workspace Secondary',
            'type' => 'workspace',
            'is_active' => true,
        ]);

        $primaryMembership = $user->organizationMemberships()->create([
            'organization_id' => $primaryOrganization->id,
            'role_code' => 'owner',
            'status' => 'active',
            'is_primary' => true,
            'joined_at' => now()->subDay(),
        ]);

        $secondaryMembership = $user->organizationMemberships()->create([
            'organization_id' => $secondaryOrganization->id,
            'role_code' => 'member',
            'status' => 'active',
            'is_primary' => false,
            'joined_at' => now(),
        ]);

        $switchResponse = $this->actingAs($user)
            ->postJson('/api/v1/context/active-membership', [
                'membership_id' => $secondaryMembership->id,
            ]);

        $switchResponse
            ->assertOk()
            ->assertJsonPath('data.context.active_organization.code', 'workspace-secondary')
            ->assertJsonPath('data.context.context_switching.strategy', 'session_selected_membership')
            ->assertJsonPath('data.context.context_switching.selected_membership_id', $secondaryMembership->id);

        $this->assertContains('organization.context.switch', $switchResponse->json('data.capabilities'));
        $this->assertContains('organization.context.read', $switchResponse->json('data.capabilities'));
        $this->assertNotContains('organization.context.manage', $switchResponse->json('data.capabilities'));

        $meResponse = $this->actingAs($user)
            ->getJson('/api/v1/me')
            ->assertOk();

        $meResponse
            ->assertOk()
            ->assertJsonPath('data.context.active_organization.code', 'workspace-secondary')
            ->assertJsonPath('data.context.context_switching.selected_membership_id', $secondaryMembership->id);

        $this->assertContains('organization.context.switch', $meResponse->json('data.capabilities'));
        $this->assertContains('organization.context.read', $meResponse->json('data.capabilities'));
        $this->assertNotContains('organization.context.manage', $meResponse->json('data.capabilities'));

        $this->assertNotSame($primaryMembership->id, $secondaryMembership->id);
    }

    public function test_active_organization_context_exposes_parent_bridge_when_membership_is_on_a_dealer(): void
    {
        $user = User::factory()->create();
        $workspace = Organization::query()->create([
            'code' => 'workspace-root',
            'name' => 'Workspace Root',
            'type' => 'workspace',
            'is_active' => true,
        ]);
        $dealer = Organization::query()->create([
            'parent_organization_id' => $workspace->id,
            'code' => 'dealer-bridge',
            'name' => 'Dealer Bridge',
            'type' => 'dealer',
            'is_active' => true,
        ]);

        $user->organizationMemberships()->create([
            'organization_id' => $dealer->id,
            'role_code' => 'member',
            'status' => 'active',
            'is_primary' => true,
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/me');

        $response
            ->assertOk()
            ->assertJsonPath('data.context.active_organization.code', 'dealer-bridge')
            ->assertJsonPath('data.context.active_organization.parent_organization.code', 'workspace-root')
            ->assertJsonPath('data.context.available_organizations.0.parent_organization_code', 'workspace-root');
    }

    public function test_dealer_seller_is_a_first_class_authenticated_actor_with_dealer_membership_context(): void
    {
        $seller = User::factory()->create([
            'email' => 'seller@dealer-example.test',
            'is_active' => true,
        ]);
        $workspace = Organization::query()->create([
            'code' => 'workspace-foundation',
            'name' => 'Workspace Foundation',
            'type' => 'workspace',
            'is_active' => true,
        ]);
        $dealer = Organization::query()->create([
            'parent_organization_id' => $workspace->id,
            'code' => 'dealer-seller-home',
            'name' => 'Dealer Seller Home',
            'type' => 'dealer',
            'is_active' => true,
        ]);

        $seller->organizationMemberships()->create([
            'organization_id' => $dealer->id,
            'role_code' => 'dealer_seller',
            'status' => 'active',
            'is_primary' => true,
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($seller)->getJson('/api/v1/me');

        $response
            ->assertOk()
            ->assertJsonPath('data.email', 'seller@dealer-example.test')
            ->assertJsonPath('data.account_status', 'active')
            ->assertJsonPath('data.context.is_bootstrap', false)
            ->assertJsonPath('data.context.active_organization.code', 'dealer-seller-home')
            ->assertJsonPath('data.context.active_organization.type', 'dealer')
            ->assertJsonPath('data.context.active_organization.parent_organization.code', 'workspace-foundation')
            ->assertJsonPath('data.context.active_membership.role_code', 'dealer_seller')
            ->assertJsonPath('data.context.active_scope.code', 'organization:dealer-seller-home')
            ->assertJsonPath('data.context.assignments', []);

        $this->assertContains('organization.context.read', $response->json('data.capabilities'));
        $this->assertContains('organization.context.active', $response->json('data.capabilities'));
        $this->assertNotContains('organization.context.manage', $response->json('data.capabilities'));
    }

    public function test_users_cannot_switch_to_a_membership_they_do_not_own(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $organization = Organization::query()->create([
            'code' => 'workspace-shared',
            'name' => 'Workspace Shared',
            'type' => 'workspace',
            'is_active' => true,
        ]);

        $otherMembership = $otherUser->organizationMemberships()->create([
            'organization_id' => $organization->id,
            'role_code' => 'owner',
            'status' => 'active',
            'is_primary' => true,
            'joined_at' => now(),
        ]);

        $this->actingAs($user)
            ->postJson('/api/v1/context/active-membership', [
                'membership_id' => $otherMembership->id,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('membership_id');
    }

    public function test_guests_cannot_fetch_their_api_profile(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized();
    }
}
