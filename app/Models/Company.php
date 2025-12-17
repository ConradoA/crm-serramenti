<?php

namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'address',
        'city',
        'cap',
        'p_iva',
        'email',
        'phone',
        'logo_path',
        'iban',
        'footer_notes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}
