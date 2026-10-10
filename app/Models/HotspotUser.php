<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotspotUser extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'router_id',
        'username',
        'password',
        'profile',
        'status',
        'router_synced',
        'last_sync_status',
        'last_sync_message',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'encrypted',
        'router_synced' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function router()
    {
        return $this->belongsTo(Router::class);
    }
}