<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Activity Log Controller
 *
 * Handles recording status updates and viewing daily activity logs.
 * Each update creates a new immutable log entry (never overwrites).
 */
class ActivityLogController extends Controller
{
    /**
     * Store a new status update for an activity.
     * Creates an immutable log entry — previous logs are preserved.
     */
    public function store(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'status' => 'required|in:done,pending',
            'remark' => 'nullable|string|max:1000',
            'date'   => 'nullable|date',
        ]);

        ActivityLog::create([
            'activity_id' => $activity->id,
            'user_id'     => $request->user()->id,
            'date'        => $validated['date'] ?? Carbon::today()->toDateString(),
            'status'      => $validated['status'],
            'remark'      => $validated['remark'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    /**
     * Display the daily activity view for a given date.
     * Shows all activities with their log history for the day.
     */
    public function dailyView(Request $request, ?string $date = null)
    {
        $date = $date ? Carbon::parse($date)->toDateString() : Carbon::today()->toDateString();

        $activities = Activity::active()
            ->orderBy('category')
            ->orderBy('title')
            ->get();

        // Get all logs for the given date, grouped by activity
        $logs = ActivityLog::forDate($date)
            ->with('user')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('activity_id');

        // Calculate navigation dates
        $prevDate = Carbon::parse($date)->subDay()->toDateString();
        $nextDate = Carbon::parse($date)->addDay()->toDateString();
        $isToday = $date === Carbon::today()->toDateString();

        return view('daily.show', compact(
            'activities',
            'logs',
            'date',
            'prevDate',
            'nextDate',
            'isToday'
        ));
    }
}
