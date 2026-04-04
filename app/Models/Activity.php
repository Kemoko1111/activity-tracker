<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Activity Model
 *
 * Represents a trackable daily activity (e.g., "Daily SMS count vs logs").
 * Each activity can have many log entries recorded by team members.
 */
class Activity extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────────────

    /**
     * An activity has many log entries (status updates).
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ─── Query Scopes ────────────────────────────────────────────────

    /**
     * Scope to only active activities.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get the latest log for a specific date.
     */
    public function latestLogForDate(string $date): ?ActivityLog
    {
        return $this->logs()
            ->whereDate('date', $date)
            ->latest()
            ->first();
    }
}
