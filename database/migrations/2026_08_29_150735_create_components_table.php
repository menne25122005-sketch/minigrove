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
        Schema::create('components', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);

            $table->string('sku', 50)->unique();

            /*
             * Các loại:
             * container  = bình
             * plant      = cây
             * moss       = rêu
             * substrate  = nền / giá thể
             * stone      = đá
             * accessory  = phụ kiện
             */
            $table->string('component_type', 30);

            $table->decimal('price', 15, 2);

            $table->text('description')->nullable();

            $table->unsignedInteger('stock_quantity')->default(0);

            // Đơn vị tồn kho: cái, cây, gói...
            $table->string('unit', 30)->default('cái');

            // Nhóm kích thước nếu cần cho Studio
            $table->string('size_group', 30)->nullable();

            /*
             * closed / open / dry / all
             */
            $table->string('compatible_ecosystem_type', 30)
                ->default('all');

            // Thông tin hỗ trợ kiểm tra tương thích/chăm sóc
            $table->text('compatibility_note')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};