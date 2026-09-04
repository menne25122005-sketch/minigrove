<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method',
        'amount',
        'status',
        'transaction_code',
        'bank_name',
        'account_name',
        'account_number',
        'transfer_content',
        'qr_code_path',
        'confirmed_by',
        'paid_at',
        'refunded_at',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function confirmer()
    {
        return $this->belongsTo(
            User::class,
            'confirmed_by'
        );
    }
}