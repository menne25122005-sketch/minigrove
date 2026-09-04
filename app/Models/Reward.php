<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'reward_type',
        'voucher_id',
        'component_id',
        'points_required',
        'description',
        'stock_quantity',
        'start_at',
        'end_at',
        'is_active',
    ];

    protected $casts = [
        'points_required' => 'integer',
        'stock_quantity' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function component()
    {
        return $this->belongsTo(Component::class);
    }

    public function redemptions()
    {
        return $this->hasMany(RewardRedemption::class);
    }
}