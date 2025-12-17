<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimateItem extends Model
{
    protected $fillable = ['estimate_id', 'product_type', 'name', 'width', 'height', 'depth', 'quantity', 'unit_price', 'total_price', 'attributes', 'photos'];

    protected $casts = [
        'attributes' => 'array',
        'photos' => 'array',
        'width' => 'integer',
        'height' => 'integer',
        'depth' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function estimate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }
}
