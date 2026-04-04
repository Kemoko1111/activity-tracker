<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Report Controller
 *
 * Provides filtered reports of activity logs with summary and detail views.
 * Supports custom date range filtering and CSV export.
 */
class ReportController extends Controller
{
    /**
     * Show the report page with date range picker.
     */
    public function index()
    {
        $activities = Activity::orderBy('title')->get();
        return view('reports.index', compact('activities'));
    }

    /**
     * Generate a filtered report based on date range and optional activity filter.
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'activity_id' => 'nullable|exists:activities,id',
            'status'      => 'nullable|in:done,pending',
        ]);

        $activities = Activity::orderBy('title')->get();

        // Build the query
        $query = ActivityLog::byDateRange($validated['start_date'], $validated['end_date'])
            ->with(['activity', 'user'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if (!empty($validated['activity_id'])) {
            $query->where('activity_id', $validated['activity_id']);
        }

        if (!empty($validated['status'])) {
            $query->withStatus($validated['status']);
        }

        $logs = $query->get();

        // Build summary data
        $summary = [
            'total_logs'    => $logs->count(),
            'done_count'    => $logs->where('status', 'done')->count(),
            'pending_count' => $logs->where('status', 'pending')->count(),
            'unique_days'   => $logs->pluck('date')->unique()->count(),
            'unique_users'  => $logs->pluck('user_id')->unique()->count(),
        ];

        // Group logs by date for detailed view
        $logsByDate = $logs->groupBy(function ($log) {
            return $log->date->format('Y-m-d');
        });

        return view('reports.index', compact(
            'activities',
            'logs',
            'logsByDate',
            'summary',
            'validated'
        ));
    }

    /**
     * Export report as CSV download.
     */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'activity_id' => 'nullable|exists:activities,id',
            'status'      => 'nullable|in:done,pending',
        ]);

        $query = ActivityLog::byDateRange($validated['start_date'], $validated['end_date'])
            ->with(['activity', 'user'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if (!empty($validated['activity_id'])) {
            $query->where('activity_id', $validated['activity_id']);
        }

        if (!empty($validated['status'])) {
            $query->withStatus($validated['status']);
        }

        $logs = $query->get();

        $filename = 'activity_report_' . $validated['start_date'] . '_to_' . $validated['end_date'] . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Activity', 'Status', 'Remark', 'Updated By', 'Timestamp']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->date->format('Y-m-d'),
                    $log->activity->title,
                    ucfirst($log->status),
                    $log->remark ?? '',
                    $log->user->name,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
