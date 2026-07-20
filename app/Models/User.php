<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',

        'profile_photo',

        'role',
        'is_suspended',
        'admin_id',

        'service_status',
        'trial_ends_at',
        'service_expires_at',

        'business_name',
        'business_phone',
        'business_till',
        'business_paybill',
        'business_account',

        'network_name',
        'business_address',

        'payment_type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'   => 'datetime',
        'password'            => 'hashed',
        'trial_ends_at'       => 'datetime',
        'service_expires_at'  => 'datetime',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function hotspotUsers()
    {
        return $this->hasMany(HotspotUser::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function customers()
    {
        return $this->hasMany(User::class, 'admin_id');
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }

        return "https://ui-avatars.com/api/?name=" .
            urlencode($this->name) .
            "&background=2563eb&color=ffffff&size=200";
    }
}