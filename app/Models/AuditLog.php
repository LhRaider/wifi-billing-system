<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    /**
     * Fields that may be populated when an audit event is recorded.
     */
    protected $fillable = [
        'user_id',
        'admin_id',
        'scope',
        'actor_name',
        'actor_email',
        'actor_role',
        'action',
        'module',
        'description',
        'ip_address',
    ];

    /**
     * The user account associated with the audit event.
     *
     * Historical actor fields are stored separately so the audit record
     * remains meaningful even if this user is later changed or deleted.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The ISP Admin tenant that owns this audit event.
     *
     * Platform-level events may have no admin_id.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}