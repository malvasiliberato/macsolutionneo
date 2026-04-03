<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legacy_entity_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('source_system');
            $table->string('legacy_table');
            $table->string('legacy_id')->nullable();
            $table->json('legacy_key')->nullable();
            $table->string('legacy_key_hash', 64);
            $table->nullableMorphs('target');
            $table->string('mapping_status')->default('mapped');
            $table->string('checksum', 64)->nullable();
            $table->timestamp('last_imported_at')->nullable();
            $table->timestamps();

            $table->unique(['source_system', 'legacy_table', 'legacy_key_hash'], 'legacy_entity_map_source_unique');
            $table->index(['target_type', 'target_id'], 'legacy_entity_map_target_index');
            $table->index(['mapping_status', 'last_imported_at'], 'legacy_entity_map_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legacy_entity_mappings');
    }
};
