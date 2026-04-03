<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Coverage;
use App\Models\DealerOperatorAssignment;
use App\Models\LegacyEntityMapping;
use App\Models\Organization;
use App\Models\OrganizationProductEnablement;
use App\Models\Product;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $platformAdminRole = Role::query()->firstOrCreate(
            ['code' => 'platform_admin'],
            [
                'name' => 'Platform Admin',
                'description' => 'Bootstrap role for local workspace setup.',
                'is_system' => true,
            ],
        );

        $bootstrapUser = User::query()->firstOrCreate(
            ['email' => 'admin@macsolution.test'],
            [
                'name' => 'Mac Solution Neo Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        );

        $bootstrapUser->roles()->syncWithoutDetaching([
            $platformAdminRole->id => ['assigned_at' => now()],
        ]);

        $bootstrapOrganization = Organization::query()->firstOrCreate(
            ['code' => 'macsolution-neo'],
            [
                'parent_organization_id' => null,
                'name' => 'Mac Solution Neo Workspace',
                'type' => 'workspace',
                'is_active' => true,
            ],
        );

        $bootstrapUser->organizationMemberships()->updateOrCreate(
            ['organization_id' => $bootstrapOrganization->id],
            [
                'role_code' => 'workspace_owner',
                'status' => 'active',
                'is_primary' => true,
                'joined_at' => now(),
            ],
        );

        $bootstrapMembership = $bootstrapUser->organizationMemberships()
            ->where('organization_id', $bootstrapOrganization->id)
            ->firstOrFail();

        $bootstrapDealer = Organization::query()->firstOrCreate(
            ['code' => 'dealer-bootstrap'],
            [
                'parent_organization_id' => $bootstrapOrganization->id,
                'name' => 'Dealer Bootstrap',
                'type' => 'dealer',
                'is_active' => true,
            ],
        );

        $bootstrapDealerSeller = User::query()->firstOrCreate(
            ['email' => 'seller@dealer-bootstrap.test'],
            [
                'name' => 'Dealer Bootstrap Seller',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        );

        $bootstrapDealerSellerMembership = $bootstrapDealerSeller->organizationMemberships()->updateOrCreate(
            ['organization_id' => $bootstrapDealer->id],
            [
                'role_code' => 'dealer_seller',
                'status' => 'active',
                'is_primary' => true,
                'joined_at' => now(),
            ],
        );

        $bootstrapAssignment = DealerOperatorAssignment::query()->updateOrCreate(
            [
                'dealer_organization_id' => $bootstrapDealer->id,
                'operator_membership_id' => $bootstrapMembership->id,
                'assignment_role_code' => 'dealer_account_manager',
            ],
            [
                'status' => 'active',
                'is_primary' => true,
                'assigned_at' => now(),
                'ended_at' => null,
            ],
        );

        $bootstrapMappings = [
            [
                'source_system' => 'legacy_bootstrap',
                'legacy_table' => 'dealer',
                'legacy_id' => 'admin-bootstrap',
                'legacy_key' => ['channel' => 'admin', 'dealer_code' => 'admin-bootstrap'],
                'target' => $bootstrapUser,
            ],
            [
                'source_system' => 'legacy_bootstrap',
                'legacy_table' => 'dealer',
                'legacy_id' => 'macsolution-neo',
                'legacy_key' => ['dealer_code' => 'macsolution-neo', 'kind' => 'organization'],
                'target' => $bootstrapOrganization,
            ],
            [
                'source_system' => 'legacy_bootstrap',
                'legacy_table' => 'dealer',
                'legacy_id' => 'dealer-bootstrap',
                'legacy_key' => ['dealer_code' => 'dealer-bootstrap', 'kind' => 'dealer'],
                'target' => $bootstrapDealer,
            ],
            [
                'source_system' => 'legacy_bootstrap',
                'legacy_table' => 'dealer_user_assignment',
                'legacy_id' => null,
                'legacy_key' => ['dealer_code' => 'macsolution-neo', 'user_email' => 'admin@macsolution.test'],
                'target' => $bootstrapMembership,
            ],
            [
                'source_system' => 'legacy_bootstrap',
                'legacy_table' => 'dealer_collaboratore',
                'legacy_id' => '501',
                'legacy_key' => ['dealer_id' => 2, 'collaborator_id' => 501],
                'target' => $bootstrapDealerSeller,
            ],
            [
                'source_system' => 'legacy_bootstrap',
                'legacy_table' => 'dealer_collaboratore',
                'legacy_id' => null,
                'legacy_key' => ['dealer_code' => 'dealer-bootstrap', 'user_email' => 'seller@dealer-bootstrap.test'],
                'target' => $bootstrapDealerSellerMembership,
            ],
            [
                'source_system' => 'legacy_bootstrap',
                'legacy_table' => 'dealer_user_assignment',
                'legacy_id' => null,
                'legacy_key' => [
                    'dealer_code' => 'dealer-bootstrap',
                    'user_email' => 'admin@macsolution.test',
                    'assignment_role_code' => 'dealer_account_manager',
                ],
                'target' => $bootstrapAssignment,
            ],
        ];

        foreach ($bootstrapMappings as $mapping) {
            /** @var \Illuminate\Database\Eloquent\Model $target */
            $target = $mapping['target'];
            $legacyKey = $mapping['legacy_key'];

            LegacyEntityMapping::query()->updateOrCreate(
                [
                    'source_system' => $mapping['source_system'],
                    'legacy_table' => $mapping['legacy_table'],
                    'legacy_key_hash' => LegacyEntityMapping::makeLegacyKeyHash($legacyKey),
                ],
                [
                    'legacy_id' => $mapping['legacy_id'],
                    'legacy_key' => $legacyKey,
                    'target_type' => $target->getMorphClass(),
                    'target_id' => $target->getKey(),
                    'mapping_status' => 'mapped',
                    'last_imported_at' => now(),
                ],
            );
        }

        $bootstrapSupplier = Supplier::query()->firstOrCreate(
            ['code' => 'macsupplier'],
            [
                'name' => 'Mac Solution Supplier',
                'is_active' => true,
            ],
        );

        $bootstrapCompany = Company::query()->firstOrCreate(
            ['code' => 'neo-insure'],
            [
                'supplier_id' => $bootstrapSupplier->id,
                'name' => 'Neo Insure Company',
                'is_active' => true,
            ],
        );

        $bootstrapProduct = Product::query()->firstOrCreate(
            ['code' => 'cvt-protection'],
            [
                'company_id' => $bootstrapCompany->id,
                'name' => 'CVT Protection',
                'kind' => 'coverage_bundle',
                'is_active' => true,
            ],
        );

        $baseCoverage = Coverage::query()->firstOrCreate(
            ['code' => 'furto-incendio'],
            [
                'name' => 'Furto e Incendio',
                'category' => 'base',
                'is_active' => true,
            ],
        );

        $gapCoverage = Coverage::query()->firstOrCreate(
            ['code' => 'gap'],
            [
                'name' => 'GAP',
                'category' => 'optional',
                'is_active' => true,
            ],
        );

        $bootstrapProduct->coverages()->syncWithoutDetaching([
            $baseCoverage->id => ['sort_order' => 1],
            $gapCoverage->id => ['sort_order' => 2],
        ]);

        OrganizationProductEnablement::query()->updateOrCreate(
            [
                'organization_id' => $bootstrapOrganization->id,
                'product_id' => $bootstrapProduct->id,
            ],
            [
                'status' => 'enabled',
                'source' => 'bootstrap',
                'enabled_at' => now(),
            ],
        );
    }
}
