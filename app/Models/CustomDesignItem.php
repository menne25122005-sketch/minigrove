<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomDesignItem extends Model
{
    use HasFactory;


    protected $fillable = [

        'custom_design_id',

        'component_type',

        'component_name',

        'unit_price',

        'quantity',

        'subtotal',

    ];



    protected $casts = [

        'unit_price' => 'decimal:2',

        'quantity' => 'integer',

        'subtotal' => 'decimal:2',

    ];



    /**
     * Item thuộc một Custom Design.
     */
    public function customDesign()
    {
        return $this->belongsTo(
            CustomDesign::class
        );
    }

}