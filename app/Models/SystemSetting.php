<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'business_name',
        'business_phone',
        'currency',

        'license_payment_type',
        'license_till_number',
        'license_paybill_number',
        'license_account_number',
        'license_phone_number',
        'license_monthly_fee',

        'mpesa_auto_activate',
        'mikrotik_real_connection',
        'default_router_id',
        'support_message',
    ];

    protected $casts = [
        'mpesa_auto_activate' => 'boolean',
        'mikrotik_real_connection' => 'boolean',
        'license_monthly_fee' => 'decimal:2',
    ];
}