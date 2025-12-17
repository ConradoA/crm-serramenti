<?php

namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use LogsActivity;

    protected $fillable = ['estimate_id', 'client_id', 'number', 'date', 'due_date', 'type', 'amount', 'status', 'notes'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
    ];

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function estimate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function interactions(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Interaction::class, 'interactionable');
    }
}
