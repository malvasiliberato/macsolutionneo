<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->foreignId('parent_organization_id')
                ->nullable()
                ->after('id')
                ->constrained('organizations')
                ->nullOnDelete();

            $table->index(['parent_organization_id', 'type'], 'org_parent_type_idx');
        });

        Schema::create('dealer_operator_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('operator_membership_id')->constrained('organization_memberships')->cascadeOnDelete();
            $table->string('assignment_role_code')->default('dealer_operator');
            $table->string('status')->default('active');
            $table->boolean('is_primary')->default(false);
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['dealer_organization_id', 'operator_membership_id', 'assignment_role_code'],
                'doa_dealer_membership_role_unq',
            );
            $table->index(['operator_membership_id', 'status', 'is_primary'], 'doa_operator_status_primary_idx');
            $table->index(['dealer_organization_id', 'status'], 'doa_dealer_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dealer_operator_assignments');

        Schema::table('organizations', function (Blueprint $table) {
            $table->dropIndex('org_parent_type_idx');
            $table->dropConstrainedForeignId('parent_organization_id');
        });
    }
};
