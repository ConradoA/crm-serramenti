<?php

namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use LogsActivity;

    protected $fillable = ['estimate_id', 'supplier_id', 'number', 'date', 'status', 'total_amount', 'notes', 'attachments'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    protected $casts = [
        'date' => 'date',
        'attachments' => 'array',
    ];

    public function supplier(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function interactions(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Interaction::class, 'interactionable');
    }

    public function estimate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }
    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
