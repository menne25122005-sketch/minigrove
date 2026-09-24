<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Các danh mục quà tặng chính thức của MiniGrove.
         */
        $categories = [
            [
                'name' => 'Quà tặng Ba Mẹ',
                'slug' => 'qua-tang-ba-me',
                'description' =>
                    'Những mẫu MiniGrove phù hợp làm quà dành tặng ba mẹ, mang ý nghĩa yêu thương, bình an và gắn kết.',
                'image' => null,
                'is_active' => true,
                'display_order' => 1,
            ],

            [
                'name' => 'Quà tặng Tết',
                'slug' => 'qua-tang-tet',
                'description' =>
                    'Những mẫu MiniGrove mang ý nghĩa may mắn, bình an, tài lộc và lời chúc tốt đẹp trong dịp năm mới.',
                'image' => null,
                'is_active' => true,
                'display_order' => 2,
            ],

            [
                'name' => 'Quà tặng Valentine',
                'slug' => 'qua-tang-valentine',
                'description' =>
                    'Những mẫu MiniGrove lãng mạn và đáng yêu dành tặng người thương trong dịp Valentine.',
                'image' => null,
                'is_active' => true,
                'display_order' => 3,
            ],

            [
                'name' => 'Quà tặng Sinh Nhật',
                'slug' => 'qua-tang-sinh-nhat',
                'description' =>
                    'Những mẫu MiniGrove phù hợp làm quà sinh nhật với thông điệp về niềm vui, ước mơ và những khởi đầu mới.',
                'image' => null,
                'is_active' => true,
                'display_order' => 4,
            ],

            [
                'name' => 'Quà tặng Phật Giáo',
                'slug' => 'qua-tang-phat-giao',
                'description' =>
                    'Những mẫu MiniGrove mang phong cách Phật giáo, hướng đến sự an nhiên, thanh tịnh và bình an.',
                'image' => null,
                'is_active' => true,
                'display_order' => 5,
            ],
        ];

        /*
         * Tắt các category mẫu cũ.
         *
         * Không xóa cứng vì sản phẩm/lịch sử cũ có thể vẫn tham chiếu.
         */
        Category::query()
            ->whereNotIn(
                'slug',
                array_column($categories, 'slug')
            )
            ->update([
                'is_active' => false,
            ]);

        foreach ($categories as $category) {
            Category::updateOrCreate(
                [
                    'slug' => $category['slug'],
                ],
                $category
            );
        }
    }
}