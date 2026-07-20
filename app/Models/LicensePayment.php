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
        'start_date',
        'expiry_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}