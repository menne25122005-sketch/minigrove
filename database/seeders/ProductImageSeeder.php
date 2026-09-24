<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImageSeeder extends Seeder
{
    private const MAX_IMAGES_PER_PRODUCT = 8;

    private const ALLOWED_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'webp',
        'gif',
    ];

    public function run(): void
    {
        $assetsRoot = database_path(
            'seeders/assets/products'
        );

        if (! File::isDirectory($assetsRoot)) {
            $this->command?->warn(
                'Không tìm thấy thư mục ảnh seed: '
                . $assetsRoot
            );

            return;
        }

        $productFolders = File::directories(
            $assetsRoot
        );

        if ($productFolders === []) {
            $this->command?->info(
                'Chưa có thư mục ảnh sản phẩm để seed.'
            );

            return;
        }

        foreach ($productFolders as $productFolder) {
            $sku = basename($productFolder);

            $product = Product::query()
                ->where('sku', $sku)
                ->first();

            if (! $product) {
                $this->command?->warn(
                    'Bỏ qua thư mục "'
                    . $sku
                    . '" vì không tìm thấy Product có SKU tương ứng.'
                );

                continue;
            }

            $imageFiles = collect(
                File::files($productFolder)
            )
                ->filter(
                    fn ($file) => in_array(
                        strtolower(
                            $file->getExtension()
                        ),
                        self::ALLOWED_EXTENSIONS,
                        true
                    )
                )
                ->sort(
                    fn ($left, $right) =>
                        strnatcasecmp(
                            $left->getFilename(),
                            $right->getFilename()
                        )
                )
                ->values();

            if ($imageFiles->isEmpty()) {
                $this->command?->warn(
                    'SKU '
                    . $sku
                    . ' chưa có ảnh hợp lệ.'
                );

                continue;
            }

            if (
                $imageFiles->count()
                > self::MAX_IMAGES_PER_PRODUCT
            ) {
                $this->command?->warn(
                    'SKU '
                    . $sku
                    . ' có hơn '
                    . self::MAX_IMAGES_PER_PRODUCT
                    . ' ảnh. Seeder chỉ lấy '
                    . self::MAX_IMAGES_PER_PRODUCT
                    . ' ảnh đầu tiên.'
                );

                $imageFiles = $imageFiles->take(
                    self::MAX_IMAGES_PER_PRODUCT
                );
            }

            $storageFolder =
                'products/seeded/'
                . Str::slug($sku);

            /*
             * Chỉ đồng bộ các ảnh do Seeder quản lý.
             * Ảnh Admin upload thủ công ở đường dẫn khác
             * sẽ không bị xóa.
             */
            $product->images()
                ->where(
                    'image_path',
                    'like',
                    $storageFolder . '/%'
                )
                ->delete();

            Storage::disk('public')
                ->deleteDirectory($storageFolder);

            /*
             * Khi một SKU có bộ ảnh seed, ảnh đầu tiên
             * của bộ seed sẽ trở thành ảnh chính.
             */
            $product->images()->update([
                'is_primary' => false,
            ]);

            foreach (
                $imageFiles as $index => $imageFile
            ) {
                $extension = strtolower(
                    $imageFile->getExtension()
                );

                $baseName = Str::slug(
                    pathinfo(
                        $imageFile->getFilename(),
                        PATHINFO_FILENAME
                    )
                );

                if ($baseName === '') {
                    $baseName = 'image';
                }

                $destinationPath =
                    $storageFolder
                    . '/'
                    . str_pad(
                        (string) ($index + 1),
                        2,
                        '0',
                        STR_PAD_LEFT
                    )
                    . '-'
                    . $baseName
                    . '.'
                    . $extension;

                Storage::disk('public')->put(
                    $destinationPath,
                    File::get(
                        $imageFile->getPathname()
                    )
                );

                $product->images()->create([
                    'image_path' => $destinationPath,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }

            $this->command?->info(
                'Đã seed '
                . $imageFiles->count()
                . ' ảnh cho '
                . $sku
                . ' - '
                . $product->name
            );
        }
    }
}
