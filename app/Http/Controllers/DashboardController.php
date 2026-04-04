<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Dashboard Controller
 *
 * Shows today's activities with their latest status for quick overview.
 * This is the main landing page after login.
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();

        // Get all active activities
        $activities = Activity::active()->orderBy('category')->orderBy('title')->get();

        // Get today's latest log per activity (subquery for efficiency)
        $latestLogs = ActivityLog::forDate($today)
            ->with('user')
            ->latest()
            ->get()
            ->groupBy('activity_id');

        // Summary stats
        $totalActivities = $activities->count();
        $doneCount = 0;
        $pendingCount = 0;

        foreach ($activities as $activity) {
            $logs = $latestLogs->get($activity->id);
            if ($logs && $logs->first()) {
                if ($logs->first()->status === 'done') {
                    $doneCount++;
                } else {
                    $pendingCount++;
                }
            } else {
                $pendingCount++; // No update yet = pending
            }
        }

        return view('dashboard', compact(
            'activities',
            'latestLogs',
            'today',
            'totalActivities',
            'doneCount',
            'pendingCount'
        ));
    }
}
