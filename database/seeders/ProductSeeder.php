<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $tropical = Category::where(
            'slug',
            'rung-nhiet-doi-mini'
        )->firstOrFail();

        $mushroom = Category::where(
            'slug',
            'khu-rung-nam'
        )->firstOrFail();

        $fairy = Category::where(
            'slug',
            'fairy-garden'
        )->firstOrFail();

        $japanese = Category::where(
            'slug',
            'rung-nhat-ban'
        )->firstOrFail();

        $jurassic = Category::where(
            'slug',
            'jurassic-mini-world'
        )->firstOrFail();

        $desert = Category::where(
            'slug',
            'sa-mac-mini'
        )->firstOrFail();

        Product::updateOrCreate(
            ['sku' => 'MG-FOREST-001'],
            [
                'category_id' => $tropical->id,
                'name' => 'Forest Calm',
                'ecosystem_type' => 'closed',

                'list_price' => 399000,
                'sale_price' => null,

                'size' => 'medium',

                'container_diameter_cm' => 18,
                'container_height_cm' => 22,

                'care_level' => 'easy',

                'light_requirement' =>
                    'Ánh sáng gián tiếp, tránh nắng gắt',

                'care_cycle_days' => 14,

                'description' =>
                    'Terrarium rừng nhiệt đới mini xanh mát và dễ chăm sóc.',

                'concept_story' =>
                    'Một khoảng rừng nhỏ mang lại cảm giác thư giãn và bình yên.',

                'care_instructions' =>
                    'Đặt nơi có ánh sáng gián tiếp và kiểm tra độ ẩm định kỳ.',

                'stock_quantity' => 20,

                'is_active' => true,
                'is_featured' => true,
                'is_grove_eligible' => true,
            ]
        );

        Product::updateOrCreate(
            ['sku' => 'MG-MUSHROOM-001'],
            [
                'category_id' => $mushroom->id,
                'name' => 'Mushroom Whisper',

                'ecosystem_type' => 'closed',

                'list_price' => 459000,
                'sale_price' => 429000,

                'size' => 'medium',

                'container_diameter_cm' => 20,
                'container_height_cm' => 24,

                'care_level' => 'easy',

                'light_requirement' =>
                    'Ánh sáng nhẹ, tránh ánh nắng trực tiếp',

                'care_cycle_days' => 14,

                'description' =>
                    'Khu rừng nấm thu nhỏ với phong cách huyền ảo.',

                'concept_story' =>
                    'Một khu rừng bí mật nơi những cây nấm nhỏ xuất hiện giữa lớp rêu xanh.',

                'care_instructions' =>
                    'Giữ độ ẩm ổn định và tránh đặt ở nơi quá nóng.',

                'stock_quantity' => 15,

                'is_active' => true,
                'is_featured' => true,
                'is_grove_eligible' => true,
            ]
        );

        Product::updateOrCreate(
            ['sku' => 'MG-FAIRY-001'],
            [
                'category_id' => $fairy->id,
                'name' => 'Fairy Garden Dream',

                'ecosystem_type' => 'open',

                'list_price' => 529000,
                'sale_price' => null,

                'size' => 'large',

                'container_diameter_cm' => 25,
                'container_height_cm' => 28,

                'care_level' => 'medium',

                'light_requirement' =>
                    'Ánh sáng tự nhiên gián tiếp',

                'care_cycle_days' => 7,

                'description' =>
                    'Khu vườn cổ tích thu nhỏ với cây xanh và phụ kiện trang trí.',

                'concept_story' =>
                    'Một thế giới cổ tích nhỏ bé được tạo nên giữa những tán cây.',

                'care_instructions' =>
                    'Kiểm tra độ ẩm hàng tuần và cắt tỉa khi cây phát triển mạnh.',

                'stock_quantity' => 10,

                'is_active' => true,
                'is_featured' => true,
                'is_grove_eligible' => true,
            ]
        );

        Product::updateOrCreate(
            ['sku' => 'MG-JAPAN-001'],
            [
                'category_id' => $japanese->id,
                'name' => 'Zen Grove',

                'ecosystem_type' => 'open',

                'list_price' => 489000,
                'sale_price' => null,

                'size' => 'medium',

                'container_diameter_cm' => 22,
                'container_height_cm' => 20,

                'care_level' => 'medium',

                'light_requirement' =>
                    'Ánh sáng dịu và thông thoáng',

                'care_cycle_days' => 7,

                'description' =>
                    'Terrarium lấy cảm hứng từ khu vườn Nhật Bản.',

                'concept_story' =>
                    'Không gian thiền thu nhỏ mang lại cảm giác cân bằng và yên tĩnh.',

                'care_instructions' =>
                    'Giữ môi trường thông thoáng và kiểm tra cây định kỳ.',

                'stock_quantity' => 12,

                'is_active' => true,
                'is_featured' => false,
                'is_grove_eligible' => true,
            ]
        );

        Product::updateOrCreate(
            ['sku' => 'MG-JURASSIC-001'],
            [
                'category_id' => $jurassic->id,
                'name' => 'Jurassic Mini World',

                'ecosystem_type' => 'open',

                'list_price' => 559000,
                'sale_price' => 519000,

                'size' => 'large',

                'container_diameter_cm' => 26,
                'container_height_cm' => 25,

                'care_level' => 'medium',

                'light_requirement' =>
                    'Ánh sáng gián tiếp',

                'care_cycle_days' => 7,

                'description' =>
                    'Thế giới khủng long thu nhỏ kết hợp cây xanh và đá trang trí.',

                'concept_story' =>
                    'Một vùng đất tiền sử được tái hiện trong một hệ sinh thái nhỏ.',

                'care_instructions' =>
                    'Kiểm tra độ ẩm, loại bỏ lá hỏng và tránh nắng trực tiếp.',

                'stock_quantity' => 8,

                'is_active' => true,
                'is_featured' => true,
                'is_grove_eligible' => true,
            ]
        );

        Product::updateOrCreate(
            ['sku' => 'MG-DESERT-001'],
            [
                'category_id' => $desert->id,
                'name' => 'Desert Stone',

                'ecosystem_type' => 'dry',

                'list_price' => 349000,
                'sale_price' => null,

                'size' => 'mini',

                'container_diameter_cm' => 16,
                'container_height_cm' => 16,

                'care_level' => 'easy',

                'light_requirement' =>
                    'Ánh sáng tốt, không để môi trường quá ẩm',

                'care_cycle_days' => 14,

                'description' =>
                    'Hệ sinh thái khô phong cách sa mạc thu nhỏ.',

                'concept_story' =>
                    'Một góc sa mạc nhỏ với đá và thực vật chịu khô.',

                'care_instructions' =>
                    'Hạn chế tưới quá nhiều và bảo đảm môi trường thông thoáng.',

                'stock_quantity' => 18,

                'is_active' => true,
                'is_featured' => false,
                'is_grove_eligible' => true,
            ]
        );
    }
}