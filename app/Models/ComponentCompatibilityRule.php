<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponentCompatibilityRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_component_id',
        'target_component_id',
        'is_compatible',
        'reason',
        'is_active',
    ];

    protected $casts = [
        'is_compatible' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Component được chọn trước.
     */
    public function sourceComponent()
    {
        return $this->belongsTo(
            Component::class,
            'source_component_id'
        );
    }

    /**
     * Component được kiểm tra tiếp theo.
     */
    public function targetComponent()
    {
        return $this->belongsTo(
            Component::class,
            'target_component_id'
        );
    }
}