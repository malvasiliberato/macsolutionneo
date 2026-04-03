<?php

namespace Tests\Feature;

use App\Models\LegacyEntityMapping;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegacyImportMappingPersistenceTest extends TestCase
{
    use DatabaseTransactions;

    protected static bool $schemaIsReady = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (! self::$schemaIsReady) {
            $this->artisan('migrate:fresh', ['--seed' => true]);
            self::$schemaIsReady = true;
        }
    }

    public function test_bootstrap_seed_creates_technical_legacy_mappings_for_auth_and_organization_foundation(): void
    {
        $user = User::query()->where('email', 'admin@macsolution.test')->firstOrFail();
        $organization = Organization::query()->where('code', 'macsolution-neo')->firstOrFail();
        $membership = $user->organizationMemberships()->where('organization_id', $organization->id)->firstOrFail();

        $this->assertCount(1, $user->legacyMappings);
        $this->assertCount(1, $organization->legacyMappings);
        $this->assertCount(1, $membership->legacyMappings);
        $this->assertSame('legacy_bootstrap', $user->legacyMappings->first()->source_system);
        $this->assertSame('dealer', $organization->legacyMappings->first()->legacy_table);
        $this->assertSame('dealer_user_assignment', $membership->legacyMappings->first()->legacy_table);
    }

    public function test_mapping_table_supports_idempotent_update_for_same_legacy_key(): void
    {
        $user = User::factory()->create();
        $legacyKey = [
            'channel' => 'dealer',
            'legacy_user_id' => 123,
        ];

        $attributes = [
            'source_system' => 'legacy_ci3',
            'legacy_table' => 'dealer_users',
            'legacy_key_hash' => LegacyEntityMapping::makeLegacyKeyHash($legacyKey),
        ];

        $first = LegacyEntityMapping::query()->updateOrCreate(
            $attributes,
            [
                'legacy_id' => '123',
                'legacy_key' => $legacyKey,
                'target_type' => $user->getMorphClass(),
                'target_id' => $user->id,
                'mapping_status' => 'mapped',
                'last_imported_at' => now()->subMinute(),
            ],
        );

        $second = LegacyEntityMapping::query()->updateOrCreate(
            $attributes,
            [
                'legacy_id' => '123',
                'legacy_key' => $legacyKey,
                'target_type' => $user->getMorphClass(),
                'target_id' => $user->id,
                'mapping_status' => 'reimported',
                'last_imported_at' => now(),
            ],
        );

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, LegacyEntityMapping::query()->where($attributes)->count());
        $this->assertSame('reimported', $second->fresh()->mapping_status);
    }
}
