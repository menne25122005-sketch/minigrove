<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_type',
        'product_id',
        'component_id',
        'transaction_type',
        'quantity_before',
        'quantity_change',
        'quantity_after',
        'order_id',
        'updated_by',
        'note',
    ];

    protected $casts = [
        'quantity_before' => 'integer',
        'quantity_change' => 'integer',
        'quantity_after' => 'integer',
    ];

    /**
     * Product nếu stock_type = PRODUCT.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Component nếu stock_type = COMPONENT.
     */
    public function component()
    {
        return $this->belongsTo(Component::class);
    }

    /**
     * Order gây ra thay đổi tồn kho.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Người thực hiện thay đổi kho.
     */
    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }
}