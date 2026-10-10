<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Router extends Model
{
    protected $fillable = [
        'admin_id',
        'name',
        'ip_address',
        'username',
        'password',
        'api_port',
        'router_type',
        'notes',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',

        /*
         * Encrypt the MikroTik password in the database.
         * Laravel will automatically decrypt it when the
         * application reads $router->password.
         */
        'password' => 'encrypted',
    ];

    /**
     * Automatically generate a secure public portal token
     * whenever a new router is created.
     */
    protected static function booted(): void
    {
        static::creating(function (Router $router) {
            if (!$router->portal_token) {
                do {
                    $token = Str::random(64);
                } while (
                    static::query()
                        ->where('portal_token', $token)
                        ->exists()
                );

                $router->portal_token = $token;
            }
        });
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}