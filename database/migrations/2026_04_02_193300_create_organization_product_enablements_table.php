<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('organization_product_enablements')) {
            Schema::create('organization_product_enablements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('status')->default('enabled');
                $table->string('source')->default('bootstrap');
                $table->timestamp('enabled_at')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('organization_product_enablements', function (Blueprint $table) {
            $table->unique(['organization_id', 'product_id'], 'uq_org_product_enablement');
            $table->index(['organization_id', 'status'], 'idx_org_product_enablement_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_product_enablements');
    }
};
