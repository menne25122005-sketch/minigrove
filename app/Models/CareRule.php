<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'source_type',
        'ecosystem_type',
        'product_id',
        'component_id',
        'task_type',
        'interval_days',
        'instructions',
        'is_active',
    ];

    protected $casts = [
        'interval_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function component()
    {
        return $this->belongsTo(Component::class);
    }

    public function schedules()
    {
        return $this->hasMany(CareSchedule::class);
    }
}