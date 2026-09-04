<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'component_type',
        'price',
        'description',
        'stock_quantity',
        'low_stock_threshold',
        'unit',
        'size_group',
        'compatible_ecosystem_type',
        'compatibility_note',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Một Component có nhiều hình ảnh.
     */
    public function images()
    {
        return $this->hasMany(ComponentImage::class);
    }

    /**
     * Product và Component có quan hệ N-N.
     */
    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_components'
        )
        ->withPivot([
            'quantity',
            'is_required',
        ])
        ->withTimestamps();
    }
    /**
 * Các quy tắc mà Component này là nguồn.
 */
public function outgoingCompatibilityRules()
{
    return $this->hasMany(
        ComponentCompatibilityRule::class,
        'source_component_id'
    );
}

/**
 * Các quy tắc mà Component này là đích.
 */
public function incomingCompatibilityRules()
{
    return $this->hasMany(
        ComponentCompatibilityRule::class,
        'target_component_id'
    );
}
/**
 * Component có thể xuất hiện trong nhiều Custom Design Item.
 */
public function customDesignItems()
{
    return $this->hasMany(CustomDesignItem::class);
}
/**
 * Các Care Rule cấu hình riêng cho Component.
 */
public function careRules()
{
    return $this->hasMany(CareRule::class);
}
/**
 * Component có thể được dùng làm phần thưởng.
 */
public function rewards()
{
    return $this->hasMany(Reward::class);
}
/**
 * Lịch sử biến động tồn kho của Component.
 */
public function stockHistories()
{
    return $this->hasMany(StockHistory::class);
}

}