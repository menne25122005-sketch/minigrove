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
        Schema::create('product_components', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('component_id')
                ->constrained('components')
                ->cascadeOnDelete();

            /*
             * Số lượng Component sử dụng
             * trong một Product hoàn thiện.
             */
            $table->unsignedInteger('quantity')
                ->default(1);

            /*
             * true = thành phần bắt buộc
             * false = thành phần trang trí/tùy chọn
             */
            $table->boolean('is_required')
                ->default(true);

            $table->timestamps();

            // Không cho gắn cùng một Component
            // hai lần vào cùng một Product.
            $table->unique([
                'product_id',
                'component_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_components');
    }
};