<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'ecosystem_type',
        'list_price',
        'sale_price',
        'size',
        'container_diameter_cm',
        'container_height_cm',
        'care_level',
        'light_requirement',
        'care_cycle_days',
        'description',
        'concept_story',
        'care_instructions',
        'stock_quantity',
'low_stock_threshold',
'is_active',
        'is_featured',
        'is_grove_eligible',

        // Warranty
        'is_warranty_eligible',
        'warranty_days',
        'warranty_policy',
    ];

    protected $casts = [
        'list_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'container_diameter_cm' => 'decimal:2',
        'container_height_cm' => 'decimal:2',
        'care_cycle_days' => 'integer',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',

        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_grove_eligible' => 'boolean',

        // Warranty
        'is_warranty_eligible' => 'boolean',
        'warranty_days' => 'integer',
    ];

    /**
     * Một Product thuộc một Category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Một Product có nhiều hình ảnh.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }



    /**
     * Product có thể xuất hiện trong nhiều Wishlist Item.
     */
    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    /**
     * Product có thể xuất hiện trong nhiều CartItem.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Product có thể xuất hiện trong nhiều OrderDetail.
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * Lịch sử biến động tồn kho của Product.
     */
    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }

    /**
     * Các Review dành cho Product.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Các Care Rule cấu hình riêng cho Product.
     */
    public function careRules()
    {
        return $this->hasMany(CareRule::class);
    }

    /**
     * Các bảo hành đã phát sinh cho Product.
     */
    public function warranties()
    {
        return $this->hasMany(Warranty::class);
    }
    /**
 * Các GroveMatch Session mà Product
 * được xếp hạng cao nhất.
 */
public function topQuizSessions()
{
    return $this->hasMany(
        QuizSession::class,
        'top_product_id'
    );
}

}