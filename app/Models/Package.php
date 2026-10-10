<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = [
        'admin_id',
        'name',
        'price',
        'duration_minutes',
        'is_speed_unlimited',
        'speed',
        'download_speed',
        'upload_speed',
        'package_type',
        'router_profile',
        'description',
        'is_active',
    ];

    protected $casts = [
        'admin_id' => 'integer',
        'price' => 'decimal:2',
        'duration_minutes' => 'integer',
        'is_speed_unlimited' => 'boolean',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Duration Display
    |--------------------------------------------------------------------------
    |
    | Every package has a duration. Duration is stored internally as minutes
    | and displayed consistently throughout the application.
    |
    | Examples:
    | 30   -> 30 Minutes
    | 60   -> 1 Hour
    | 90   -> 1 Hour 30 Minutes
    | 120  -> 2 Hours
    | 1440 -> 1 Day
    |
    */

    public function getFormattedDurationAttribute(): string
    {
        $totalMinutes = (int) $this->duration_minutes;

        if ($totalMinutes <= 0) {
            return 'Not Set';
        }

        $days = intdiv($totalMinutes, 1440);
        $remainingMinutes = $totalMinutes % 1440;

        $hours = intdiv($remainingMinutes, 60);
        $minutes = $remainingMinutes % 60;

        $parts = [];

        if ($days > 0) {
            $parts[] = $days
                .' '
                .($days === 1 ? 'Day' : 'Days');
        }

        if ($hours > 0) {
            $parts[] = $hours
                .' '
                .($hours === 1 ? 'Hour' : 'Hours');
        }

        if ($minutes > 0) {
            $parts[] = $minutes
                .' '
                .($minutes === 1 ? 'Minute' : 'Minutes');
        }

        return implode(' ', $parts);
    }

    /*
    |--------------------------------------------------------------------------
    | Speed Display
    |--------------------------------------------------------------------------
    |
    | Unlimited applies to SPEED, never to package duration.
    |
    | Limited examples:
    | 5M / 5M
    | 10M / 5M
    |
    | Unlimited-speed packages have no configured download or upload cap.
    |
    */

    public function getFormattedSpeedAttribute(): string
    {
        if ($this->is_speed_unlimited) {
            return 'Unlimited';
        }

        $downloadSpeed = trim(
            (string) $this->download_speed
        );

        $uploadSpeed = trim(
            (string) $this->upload_speed
        );

        if (
            $downloadSpeed !== ''
            && $uploadSpeed !== ''
        ) {
            return $downloadSpeed.'/'.$uploadSpeed;
        }

        $legacySpeed = trim(
            (string) $this->speed
        );

        if ($legacySpeed !== '') {
            return $legacySpeed;
        }

        return 'Not Set';
    }
}