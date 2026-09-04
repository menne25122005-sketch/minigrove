<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('component_compatibility_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('source_component_id')
                ->constrained('components')
                ->cascadeOnDelete();

            $table->foreignId('target_component_id')
                ->constrained('components')
                ->cascadeOnDelete();

            $table->boolean('is_compatible');

            $table->text('reason')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['source_component_id', 'target_component_id'],
                'ccr_source_target_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_compatibility_rules');
    }
};