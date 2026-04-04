<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ActivityLog Model
 *
 * Represents an immutable log entry for an activity status update.
 * Each log records who updated, the status (done/pending), a remark,
 * and the exact timestamp. Logs are never overwritten — new entries
 * are always appended.
 */
class ActivityLog extends Model
{
    protected $fillable = [
        'activity_id',
        'user_id',
        'date',
        'status',
        'remark',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // ─── Relationships ───────────────────────────────────────────────

    /**
     * The activity this log belongs to.
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * The user who created this log entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Query Scopes ────────────────────────────────────────────────

    /**
     * Scope to filter logs for a specific date.
     */
    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope to filter logs within a date range.
     */
    public function scopeByDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereDate('date', '>=', $startDate)
                     ->whereDate('date', '<=', $endDate);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
