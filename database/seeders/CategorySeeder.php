<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Rừng nhiệt đới mini',
                'slug' => 'rung-nhiet-doi-mini',
                'description' => 'Hệ sinh thái mini mang phong cách rừng nhiệt đới xanh mát.',
                'display_order' => 1,
            ],
            [
                'name' => 'Khu rừng nấm',
                'slug' => 'khu-rung-nam',
                'description' => 'Terrarium lấy cảm hứng từ khu rừng nấm nhỏ đầy màu sắc.',
                'display_order' => 2,
            ],
            [
                'name' => 'Thung lũng mini',
                'slug' => 'thung-lung-mini',
                'description' => 'Không gian thiên nhiên thu nhỏ mô phỏng thung lũng xanh.',
                'display_order' => 3,
            ],
            [
                'name' => 'Khu rừng cổ tích',
                'slug' => 'khu-rung-co-tich',
                'description' => 'Hệ sinh thái mini mang phong cách cổ tích và huyền ảo.',
                'display_order' => 4,
            ],
            [
                'name' => 'Ngôi nhà trong rừng',
                'slug' => 'ngoi-nha-trong-rung',
                'description' => 'Terrarium kết hợp cây xanh và mô hình ngôi nhà thu nhỏ.',
                'display_order' => 5,
            ],
            [
                'name' => 'Hệ sinh thái ven biển',
                'slug' => 'he-sinh-thai-ven-bien',
                'description' => 'Không gian mini lấy cảm hứng từ cảnh quan ven biển.',
                'display_order' => 6,
            ],
            [
                'name' => 'Sa mạc mini',
                'slug' => 'sa-mac-mini',
                'description' => 'Hệ sinh thái khô với phong cách sa mạc thu nhỏ.',
                'display_order' => 7,
            ],
            [
                'name' => 'Rừng Nhật Bản',
                'slug' => 'rung-nhat-ban',
                'description' => 'Không gian thiên nhiên thu nhỏ lấy cảm hứng từ Nhật Bản.',
                'display_order' => 8,
            ],
            [
                'name' => 'Fairy Garden',
                'slug' => 'fairy-garden',
                'description' => 'Khu vườn cổ tích thu nhỏ với cây và phụ kiện trang trí.',
                'display_order' => 9,
            ],
            [
                'name' => 'Jurassic Mini World',
                'slug' => 'jurassic-mini-world',
                'description' => 'Thế giới khủng long thu nhỏ kết hợp cây xanh và mô hình.',
                'display_order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                [
                    'slug' => $category['slug'],
                ],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'image' => null,
                    'is_active' => true,
                    'display_order' => $category['display_order'],
                ]
            );
        }
    }
}