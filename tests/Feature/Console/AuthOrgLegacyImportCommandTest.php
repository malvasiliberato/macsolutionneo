<?php

namespace Tests\Feature\Console;

use App\Application\Auth\LegacyImport\AuthOrgLegacyDryRun;
use App\Models\LegacyEntityMapping;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuthOrgLegacyImportCommandTest extends TestCase
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

        $this->setUpLegacySnapshotTables();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('legacy_test_dealer_user_assignment');
        Schema::dropIfExists('legacy_test_dealer');

        parent::tearDown();
    }

    public function test_auth_org_legacy_import_command_supports_dry_run_without_writing_new_mappings(): void
    {
        $beforeCount = LegacyEntityMapping::query()->count();

        $this->artisan('legacy:import:auth-org', [
            '--dry-run' => true,
            '--source-system' => 'legacy_ci3',
            '--legacy-table' => 'all',
            '--dataset' => 'bootstrap-auth-org',
            '--batch' => 10,
        ])
            ->expectsOutput('Auth/Org legacy import dry-run summary')
            ->expectsOutput('source_system=legacy_ci3')
            ->expectsOutput('legacy_table=all')
            ->expectsOutput('dataset=bootstrap-auth-org')
            ->expectsOutput('dry_run=true')
            ->expectsOutput('records_read=8')
            ->expectsOutput('user_candidates=3')
            ->expectsOutput('organization_candidates=1')
            ->expectsOutput('membership_candidates=3')
            ->expectsOutput('assignment_candidates=1')
            ->expectsOutput('mappings_created=0')
            ->expectsOutput('mappings_updated=0')
            ->expectsOutput('collisions=1')
            ->expectsOutput('unresolved=1')
            ->expectsOutput('needs_review=0')
            ->expectsOutput('resolved_candidates=6')
            ->expectsOutput('matched_existing_targets=6')
            ->expectsOutput('unmatched_candidates=0')
            ->expectsOutput('ambiguous_matches=0')
            ->expectsOutput('heuristic_matches=6')
            ->expectsOutput('mapping_matches=0')
            ->expectsOutput('high_confidence_matches=3')
            ->expectsOutput('medium_confidence_matches=3')
            ->expectsOutput('low_confidence_matches=0')
            ->expectsOutput('no_confidence_matches=2')
            ->expectsOutput('batch_result=needs_reconciliation')
            ->expectsOutput('Auth/Org legacy reconciliation snapshot')
            ->expectsOutput('auto_match_candidates=6')
            ->expectsOutput('importable_high_confidence=3')
            ->expectsOutput('manual_review_candidates=0')
            ->expectsOutput('blocked_candidates=2')
            ->expectsOutput('commit_mode=not_available')
            ->assertSuccessful();

        $this->assertSame($beforeCount, LegacyEntityMapping::query()->count());
    }

    public function test_auth_org_legacy_import_command_rejects_commit_mode_for_now(): void
    {
        $this->artisan('legacy:import:auth-org', [
            '--source-system' => 'legacy_ci3',
        ])
            ->expectsOutput('Commit mode is not available in this slice. Re-run with --dry-run or --commit-create-only.')
            ->assertExitCode(2);
    }

    public function test_auth_org_legacy_import_command_requires_explicit_confirmation_for_create_only_commit_mode(): void
    {
        $this->artisan('legacy:import:auth-org', [
            '--source-system' => 'legacy_ci3',
            '--commit-create-only' => true,
        ])
            ->expectsOutput('Create-only commit mode requires --confirm-create-only.')
            ->assertExitCode(2);
    }

    public function test_auth_org_legacy_import_command_can_use_the_read_only_legacy_adapter(): void
    {
        $beforeCount = LegacyEntityMapping::query()->count();

        $this->artisan('legacy:import:auth-org', [
            '--dry-run' => true,
            '--source-system' => 'legacy_ci3',
            '--legacy-table' => 'all',
            '--dataset' => 'legacy-ci3-auth-org',
            '--batch' => 10,
        ])
            ->expectsOutput('Auth/Org legacy import dry-run summary')
            ->expectsOutput('dataset=legacy-ci3-auth-org')
            ->expectsOutput('records_read=4')
            ->expectsOutput('user_candidates=1')
            ->expectsOutput('organization_candidates=1')
            ->expectsOutput('membership_candidates=1')
            ->expectsOutput('assignment_candidates=1')
            ->expectsOutput('collisions=0')
            ->expectsOutput('unresolved=0')
            ->expectsOutput('needs_review=0')
            ->expectsOutput('resolved_candidates=4')
            ->expectsOutput('matched_existing_targets=4')
            ->expectsOutput('unmatched_candidates=0')
            ->expectsOutput('ambiguous_matches=0')
            ->expectsOutput('heuristic_matches=4')
            ->expectsOutput('mapping_matches=0')
            ->expectsOutput('high_confidence_matches=2')
            ->expectsOutput('medium_confidence_matches=2')
            ->expectsOutput('low_confidence_matches=0')
            ->expectsOutput('no_confidence_matches=0')
            ->expectsOutput('batch_result=dry_run_ok')
            ->expectsOutput('auto_match_candidates=4')
            ->expectsOutput('importable_high_confidence=2')
            ->assertSuccessful();

        $this->assertSame($beforeCount, LegacyEntityMapping::query()->count());
    }

    public function test_auth_org_legacy_import_command_flags_edge_cases_for_reconciliation(): void
    {
        $beforeCount = LegacyEntityMapping::query()->count();

        $this->artisan('legacy:import:auth-org', [
            '--dry-run' => true,
            '--source-system' => 'legacy_ci3',
            '--legacy-table' => 'all',
            '--dataset' => 'bootstrap-auth-org-edge-cases',
            '--batch' => 10,
        ])
            ->expectsOutput('Auth/Org legacy import dry-run summary')
            ->expectsOutput('dataset=bootstrap-auth-org-edge-cases')
            ->expectsOutput('records_read=3')
            ->expectsOutput('user_candidates=0')
            ->expectsOutput('organization_candidates=1')
            ->expectsOutput('membership_candidates=1')
            ->expectsOutput('assignment_candidates=1')
            ->expectsOutput('collisions=0')
            ->expectsOutput('unresolved=0')
            ->expectsOutput('needs_review=0')
            ->expectsOutput('resolved_candidates=3')
            ->expectsOutput('matched_existing_targets=0')
            ->expectsOutput('unmatched_candidates=0')
            ->expectsOutput('ambiguous_matches=3')
            ->expectsOutput('heuristic_matches=0')
            ->expectsOutput('mapping_matches=0')
            ->expectsOutput('high_confidence_matches=0')
            ->expectsOutput('medium_confidence_matches=0')
            ->expectsOutput('low_confidence_matches=3')
            ->expectsOutput('no_confidence_matches=0')
            ->expectsOutput('batch_result=needs_reconciliation')
            ->expectsOutput('manual_review_candidates=3')
            ->assertSuccessful();

        $this->assertSame($beforeCount, LegacyEntityMapping::query()->count());
    }

    public function test_auth_org_legacy_import_command_hardens_dealer_scoped_signals_without_promoting_dealer_admins(): void
    {
        $beforeCount = LegacyEntityMapping::query()->count();

        $this->artisan('legacy:import:auth-org', [
            '--dry-run' => true,
            '--source-system' => 'legacy_ci3',
            '--legacy-table' => 'all',
            '--dataset' => 'bootstrap-auth-org-signal-hardening',
            '--batch' => 10,
        ])
            ->expectsOutput('Auth/Org legacy import dry-run summary')
            ->expectsOutput('dataset=bootstrap-auth-org-signal-hardening')
            ->expectsOutput('records_read=3')
            ->expectsOutput('user_candidates=2')
            ->expectsOutput('organization_candidates=0')
            ->expectsOutput('membership_candidates=1')
            ->expectsOutput('assignment_candidates=0')
            ->expectsOutput('matched_existing_targets=0')
            ->expectsOutput('unmatched_candidates=2')
            ->expectsOutput('ambiguous_matches=1')
            ->expectsOutput('high_confidence_matches=0')
            ->expectsOutput('medium_confidence_matches=0')
            ->expectsOutput('low_confidence_matches=1')
            ->expectsOutput('no_confidence_matches=2')
            ->expectsOutput('batch_result=needs_reconciliation')
            ->expectsOutput('ready_create_candidates=1')
            ->expectsOutput('manual_review_candidates=2')
            ->expectsOutput('entity_breakdown={"user":{"ready_link":0,"ready_create":1,"needs_review":1,"blocked":0},"organization":{"ready_link":0,"ready_create":0,"needs_review":0,"blocked":0},"membership":{"ready_link":0,"ready_create":0,"needs_review":1,"blocked":0},"assignment":{"ready_link":0,"ready_create":0,"needs_review":0,"blocked":0}}')
            ->expectsOutput('lane_breakdown={"internal_platform_principal":{"ready_link":0,"ready_create":0,"needs_review":1,"blocked":0},"dealer_seller":{"ready_link":0,"ready_create":1,"needs_review":1,"blocked":0}}')
            ->expectsOutput('review_reason_breakdown={"membership_candidate_requires_resolved_organization_dependency":1,"dealer_admin_requires_manual_identity_resolution":1}')
            ->assertSuccessful();

        $this->assertSame($beforeCount, LegacyEntityMapping::query()->count());
    }

    public function test_auth_org_legacy_import_command_can_commit_create_only_candidates_idempotently(): void
    {
        $beforeMappings = LegacyEntityMapping::query()->count();

        $this->artisan('legacy:import:auth-org', [
            '--source-system' => 'legacy_ci3',
            '--legacy-table' => 'all',
            '--dataset' => 'bootstrap-auth-org-create-only',
            '--batch' => 20,
            '--commit-create-only' => true,
            '--confirm-create-only' => true,
        ])
            ->expectsOutput('Auth/Org create-only commit summary')
            ->expectsOutput('gate_mode=create_only')
            ->expectsOutput('records_scanned=8')
            ->expectsOutput('ready_create_candidates=7')
            ->expectsOutput('manual_review_candidates=1')
            ->expectsOutput('blocked_candidates=0')
            ->expectsOutput('committed_users=2')
            ->expectsOutput('committed_organizations=1')
            ->expectsOutput('committed_memberships=2')
            ->expectsOutput('committed_assignments=1')
            ->expectsOutput('mappings_created=6')
            ->expectsOutput('skipped_conflicts=1')
            ->expectsOutput('synthetic_emails_assigned=1')
            ->expectsOutput('gate_result=create_only_commit_ok')
            ->expectsOutput('excluded_review_candidates=1')
            ->expectsOutput('excluded_review_reasons={"dealer_admin_requires_manual_identity_resolution":1}')
            ->assertSuccessful();

        $this->assertSame($beforeMappings + 6, LegacyEntityMapping::query()->count());
        $this->assertDatabaseHas('organizations', ['code' => 'dealer-gamma']);
        $this->assertDatabaseHas('users', ['email' => 'operator@dealer-gamma.test']);
        $this->assertDatabaseHas('organization_memberships', ['role_code' => 'dealer_seller']);
        $this->assertDatabaseHas('dealer_operator_assignments', ['assignment_role_code' => 'dealer_operator']);
        $this->assertDatabaseMissing('users', ['name' => 'dealer.gamma.admin']);

        $syntheticEmail = \App\Models\User::query()
            ->where('name', 'Seller Gamma')
            ->value('email');

        $this->assertNotNull($syntheticEmail);
        $this->assertStringContainsString('@legacy-auth.macsolution-neo.local', $syntheticEmail);

        $this->artisan('legacy:import:auth-org', [
            '--source-system' => 'legacy_ci3',
            '--legacy-table' => 'all',
            '--dataset' => 'bootstrap-auth-org-create-only',
            '--batch' => 20,
            '--commit-create-only' => true,
            '--confirm-create-only' => true,
        ])
            ->expectsOutput('Auth/Org create-only commit summary')
            ->expectsOutput('committed_users=0')
            ->expectsOutput('committed_organizations=0')
            ->expectsOutput('committed_memberships=0')
            ->expectsOutput('committed_assignments=0')
            ->assertSuccessful();

        $this->assertSame($beforeMappings + 6, LegacyEntityMapping::query()->count());
    }

    public function test_auth_org_legacy_import_command_downgrades_create_only_candidates_with_missing_dependencies(): void
    {
        $beforeCount = LegacyEntityMapping::query()->count();

        $this->artisan('legacy:import:auth-org', [
            '--dry-run' => true,
            '--source-system' => 'legacy_ci3',
            '--legacy-table' => 'all',
            '--dataset' => 'bootstrap-auth-org-dependency-guards',
            '--batch' => 20,
        ])
            ->expectsOutput('Auth/Org legacy import dry-run summary')
            ->expectsOutput('dataset=bootstrap-auth-org-dependency-guards')
            ->expectsOutput('records_read=4')
            ->expectsOutput('ready_create_candidates=2')
            ->expectsOutput('manual_review_candidates=2')
            ->expectsOutput('blocked_candidates=0')
            ->expectsOutput('entity_breakdown={"user":{"ready_link":0,"ready_create":0,"needs_review":0,"blocked":0},"organization":{"ready_link":0,"ready_create":1,"needs_review":0,"blocked":0},"membership":{"ready_link":0,"ready_create":0,"needs_review":2,"blocked":0},"assignment":{"ready_link":0,"ready_create":1,"needs_review":0,"blocked":0}}')
            ->expectsOutput('lane_breakdown={"dealer_organization":{"ready_link":0,"ready_create":1,"needs_review":0,"blocked":0},"dealer_seller":{"ready_link":0,"ready_create":0,"needs_review":1,"blocked":0},"dealer_operator":{"ready_link":0,"ready_create":1,"needs_review":1,"blocked":0}}')
            ->expectsOutput('review_reason_breakdown={"membership_candidate_requires_resolved_user_dependency":2}')
            ->assertSuccessful();

        $this->assertSame($beforeCount, LegacyEntityMapping::query()->count());
    }

    public function test_auth_org_legacy_import_command_reports_semantic_lanes_for_bootstrap_dataset(): void
    {
        $this->artisan('legacy:import:auth-org', [
            '--dry-run' => true,
            '--source-system' => 'legacy_ci3',
            '--legacy-table' => 'all',
            '--dataset' => 'bootstrap-auth-org-create-only',
            '--batch' => 20,
        ])
            ->expectsOutput('Auth/Org legacy import dry-run summary')
            ->assertSuccessful();

        $report = app(AuthOrgLegacyDryRun::class)->run(
            'legacy_ci3',
            'all',
            'bootstrap-auth-org-create-only',
            20,
            true,
        );

        $laneBreakdown = $report['reconciliation']['lane_breakdown'];
        $laneSubtypeBreakdown = $report['reconciliation']['lane_subtype_breakdown'];
        $internalPrincipalCategoryBreakdown = $report['reconciliation']['internal_principal_category_breakdown'];

        $this->assertArrayHasKey('internal_platform_principal', $laneBreakdown);
        $this->assertArrayHasKey('dealer_organization', $laneBreakdown);
        $this->assertArrayHasKey('dealer_seller', $laneBreakdown);
        $this->assertArrayHasKey('dealer_operator', $laneBreakdown);

        $this->assertSame(1, array_sum($laneBreakdown['internal_platform_principal']));
        $this->assertSame(1, array_sum($laneBreakdown['dealer_organization']));
        $this->assertSame(2, array_sum($laneBreakdown['dealer_seller']));
        $this->assertSame(4, array_sum($laneBreakdown['dealer_operator']));
        $this->assertSame(1, $laneBreakdown['internal_platform_principal']['needs_review']);
        $this->assertArrayHasKey('internal_platform_principal', $laneSubtypeBreakdown);
        $this->assertArrayHasKey('unclassified_internal_principal', $laneSubtypeBreakdown['internal_platform_principal']);
        $this->assertSame(1, array_sum($laneSubtypeBreakdown['internal_platform_principal']['unclassified_internal_principal']));
        $this->assertArrayHasKey('dealer_operator', $laneSubtypeBreakdown);
        $this->assertArrayHasKey('dealer_operator', $laneSubtypeBreakdown['dealer_operator']);
        $this->assertArrayHasKey('dealer_seller', $laneSubtypeBreakdown['dealer_operator']);
        $this->assertArrayHasKey('human_plausible', $internalPrincipalCategoryBreakdown);
        $this->assertSame(1, array_sum($internalPrincipalCategoryBreakdown['human_plausible']));
    }

    public function test_auth_org_legacy_import_command_reports_internal_principal_review_actions(): void
    {
        $this->artisan('legacy:import:auth-org', [
            '--dry-run' => true,
            '--source-system' => 'legacy_ci3',
            '--legacy-table' => 'all',
            '--dataset' => 'bootstrap-auth-org-internal-principal-review',
            '--batch' => 20,
        ])
            ->expectsOutput('Auth/Org legacy import dry-run summary')
            ->assertSuccessful();

        $report = app(AuthOrgLegacyDryRun::class)->run(
            'legacy_ci3',
            'all',
            'bootstrap-auth-org-internal-principal-review',
            20,
            true,
        );

        $laneBreakdown = $report['reconciliation']['lane_breakdown'];
        $laneSubtypeBreakdown = $report['reconciliation']['lane_subtype_breakdown'];
        $internalPrincipalCategoryBreakdown = $report['reconciliation']['internal_principal_category_breakdown'];
        $internalPrincipalReviewActionBreakdown = $report['reconciliation']['internal_principal_review_action_breakdown'];

        $this->assertSame(5, array_sum($laneBreakdown['internal_platform_principal']));
        $this->assertArrayHasKey('supporto_tecnico', $laneSubtypeBreakdown['internal_platform_principal']);
        $this->assertArrayHasKey('superadmin', $laneSubtypeBreakdown['internal_platform_principal']);
        $this->assertArrayHasKey('backoffice_operativo', $laneSubtypeBreakdown['internal_platform_principal']);
        $this->assertArrayHasKey('technical_or_test', $laneSubtypeBreakdown['internal_platform_principal']);
        $this->assertArrayHasKey('corporate_ambiguous', $laneSubtypeBreakdown['internal_platform_principal']);
        $this->assertSame(3, array_sum($internalPrincipalCategoryBreakdown['human_plausible']));
        $this->assertSame(1, array_sum($internalPrincipalCategoryBreakdown['technical_or_test']));
        $this->assertSame(1, array_sum($internalPrincipalCategoryBreakdown['corporate']));
        $this->assertSame(1, array_sum($internalPrincipalReviewActionBreakdown['manual_link_candidate']));
        $this->assertSame(2, array_sum($internalPrincipalReviewActionBreakdown['manual_review_only']));
        $this->assertSame(1, array_sum($internalPrincipalReviewActionBreakdown['do_not_migrate_automatically']));
        $this->assertSame(1, array_sum($internalPrincipalReviewActionBreakdown['manual_target_decision_required']));
    }

    private function setUpLegacySnapshotTables(): void
    {
        Schema::dropIfExists('legacy_test_dealer_user_assignment');
        Schema::dropIfExists('legacy_test_dealer');

        Schema::create('legacy_test_dealer', function (Blueprint $table) {
            $table->id();
            $table->string('tipo')->nullable();
            $table->string('email')->nullable();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
        });

        Schema::create('legacy_test_dealer_user_assignment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dealer_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_email')->nullable();
            $table->string('organization_code')->nullable();
            $table->string('dealer_code')->nullable();
            $table->string('assignment_role_code')->nullable();
        });

        DB::table('legacy_test_dealer')->insert([
            ['id' => 1, 'tipo' => 'admin', 'email' => 'admin@macsolution.test', 'code' => 'admin-bootstrap', 'name' => 'Mac Solution Neo Admin'],
            ['id' => 2, 'tipo' => 'dealer', 'email' => null, 'code' => 'dealer-bootstrap', 'name' => 'Dealer Bootstrap'],
        ]);

        DB::table('legacy_test_dealer_user_assignment')->insert([
            [
                'id' => 1,
                'dealer_id' => 2,
                'user_id' => 10,
                'user_email' => 'admin@macsolution.test',
                'organization_code' => 'macsolution-neo',
                'dealer_code' => 'dealer-bootstrap',
                'assignment_role_code' => 'dealer_account_manager',
            ],
        ]);
    }
}
