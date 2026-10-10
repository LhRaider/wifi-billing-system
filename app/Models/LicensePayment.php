<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicensePayment extends Model
{
    protected $fillable = [
        'user_id',
        'months',
        'amount',
        'payment_method',
        'transaction_code',

        'checkout_request_id',
        'merchant_request_id',
        'mpesa_receipt',
        'phone_number',
        'paid_at',

        'start_date',
        'expiry_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}