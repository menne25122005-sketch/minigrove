<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponentImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'component_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Một ComponentImage thuộc một Component.
     */
    public function component()
    {
        return $this->belongsTo(Component::class);
    }
}