<?php

namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'company_name', 'email', 'phone', 'vat_number', 'fiscal_code', 'address', 'city', 'cap', 'notes'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function estimates(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Estimate::class);
    }
    //
}
