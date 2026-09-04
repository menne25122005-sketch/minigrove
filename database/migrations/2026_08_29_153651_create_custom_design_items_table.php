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
        Schema::create('custom_design_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('custom_design_id')
                ->constrained('custom_designs')
                ->cascadeOnDelete();

            $table->foreignId('component_id')
                ->constrained('components')
                ->restrictOnDelete();

            /*
             * Snapshot loại Component.
             * container / plant / moss /
             * substrate / stone / accessory
             */
            $table->string('component_type', 30);

            // Snapshot tên tại thời điểm thiết kế
            $table->string('component_name', 150);

            // Snapshot giá tại thời điểm thiết kế
            $table->decimal('unit_price', 15, 2);

            $table->unsignedInteger('quantity')->default(1);

            $table->decimal('subtotal', 15, 2);

            $table->timestamps();

            /*
             * Một Component chỉ xuất hiện một dòng
             * trong cùng một Custom Design.
             */
            $table->unique(
                ['custom_design_id', 'component_id'],
                'cdi_design_component_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_design_items');
    }
};