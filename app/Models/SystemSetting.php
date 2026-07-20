<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'business_name',
        'business_phone',
        'currency',
        'mpesa_auto_activate',
        'mikrotik_real_connection',
        'default_router_id',
        'support_message',
    ];

    protected $casts = [
        'mpesa_auto_activate' => 'boolean',
        'mikrotik_real_connection' => 'boolean',
    ];
}