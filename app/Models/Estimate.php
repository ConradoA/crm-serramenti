<?php

namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    use LogsActivity;

    protected $fillable = ['client_id', 'number', 'date', 'valid_until', 'status', 'subtotal', 'installation_amount', 'tax_amount', 'total', 'internal_notes', 'public_notes'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    protected $casts = [
        'date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'installation_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function interactions(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Interaction::class, 'interactionable');
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EstimateItem::class);
    }

    public function recalculateTotals(): void
    {
        $this->subtotal = $this->items->sum('total_price');
        // Total Base = Subtotal (Materiali) + Posa
        $taxable = $this->subtotal + ($this->installation_amount ?? 0);
        $this->tax_amount = $taxable * 0.22; // 22% IVA su tutto
        $this->total = $taxable + $this->tax_amount;
        $this->saveQuietly(); // Avoid triggering events again
    }
}
