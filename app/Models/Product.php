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

        /*
         * Phân loại sản phẩm:
         * plant
         * container
         * moss
         * substrate
         * stone
         * accessory
         * grove
         */
        'ecosystem_type',

        'list_price',

        'sale_price',

        'size',

        'weight',

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

        'weight' => 'integer',
        
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
     * Product thuộc một Category.
     *
     * Ví dụ:
     * - Cây cảnh
     * - Chậu/bình
     * - Trang trí
     * - Grove
     */
    public function category()
    {
        return $this->belongsTo(
            Category::class
        );
    }





    /**
     * Một Product có nhiều hình ảnh.
     */
    public function images()
    {
        return $this->hasMany(
            ProductImage::class
        );
    }





    /**
     * Product có thể xuất hiện trong Wishlist.
     */
    public function wishlistItems()
    {
        return $this->hasMany(
            WishlistItem::class
        );
    }





    /**
     * Product có thể xuất hiện trong Cart.
     */
    public function cartItems()
    {
        return $this->hasMany(
            CartItem::class
        );
    }





    /**
     * Product xuất hiện trong Order Detail.
     */
    public function orderDetails()
    {
        return $this->hasMany(
            OrderDetail::class
        );
    }





    /**
     * Lịch sử biến động tồn kho.
     */
    public function stockHistories()
    {
        return $this->hasMany(
            StockHistory::class
        );
    }





    /**
     * Review của Product.
     */
    public function reviews()
    {
        return $this->hasMany(
            Review::class
        );
    }





    /**
     * Quy tắc chăm sóc của Product.
     */
    public function careRules()
    {
        return $this->hasMany(
            CareRule::class
        );
    }





    /**
     * Bảo hành phát sinh từ Product.
     */
    public function warranties()
    {
        return $this->hasMany(
            Warranty::class
        );
    }





    /**
     * Product được đề xuất cao nhất
     * trong GroveMatch.
     */
    public function topQuizSessions()
    {
        return $this->hasMany(
            QuizSession::class,
            'top_product_id'
        );
    }

}