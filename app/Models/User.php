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

        /*
        |--------------------------------------------------------------------------
        | Admin M-Pesa Settings
        |--------------------------------------------------------------------------
        */

        'mpesa_environment',
        'mpesa_consumer_key',
        'mpesa_consumer_secret',
        'mpesa_shortcode',
        'mpesa_passkey',
        'mpesa_transaction_type',
        'mpesa_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',

        /*
        |--------------------------------------------------------------------------
        | Sensitive M-Pesa Credentials
        |--------------------------------------------------------------------------
        */

        'mpesa_consumer_key',
        'mpesa_consumer_secret',
        'mpesa_passkey',
    ];

    protected $casts = [
        'email_verified_at'  => 'datetime',
        'password'           => 'hashed',

        'is_suspended'       => 'boolean',

        'trial_ends_at'      => 'datetime',
        'service_expires_at' => 'datetime',

        'mpesa_enabled'      => 'boolean',

        /*
        |--------------------------------------------------------------------------
        | Encrypt Sensitive M-Pesa Credentials
        |--------------------------------------------------------------------------
        */

        'mpesa_consumer_key'    => 'encrypted',
        'mpesa_consumer_secret' => 'encrypted',
        'mpesa_passkey'         => 'encrypted',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Profile Photo
    |--------------------------------------------------------------------------
    */

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset(
                'storage/' . $this->profile_photo
            );
        }

        return "https://ui-avatars.com/api/?name=" .
            urlencode($this->name) .
            "&background=2563eb&color=ffffff&size=200";
    }
}