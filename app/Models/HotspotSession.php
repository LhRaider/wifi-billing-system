<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotspotSession extends Model
{
    protected $fillable = [
        'user_id',
        'mac_address',
        'ip_address',
        'login_time',
        'logout_time',
        'data_used',
    ];

    protected $casts = [
        'login_time' => 'datetime',
        'logout_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}