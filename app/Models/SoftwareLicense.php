<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareLicense extends Model
{
    protected $fillable = [
        'client_name',
        'plan_name',
        'monthly_fee',
        'start_date',
        'expiry_date',
        'status',
        'grace_days',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
    ];
}