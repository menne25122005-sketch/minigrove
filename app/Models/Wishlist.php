<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    /**
     * Wishlist thuộc một User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Wishlist có nhiều Item.
     */
    public function items()
    {
        return $this->hasMany(WishlistItem::class);
    }
}