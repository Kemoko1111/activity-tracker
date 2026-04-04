<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

/**
 * Activity Controller
 *
 * CRUD operations for managing trackable activities.
 * Create/edit/delete are restricted to admin users via middleware.
 */
class ActivityController extends Controller
{
    /**
     * Display all activities.
     */
    public function index()
    {
        $activities = Activity::orderBy('category')
            ->orderBy('title')
            ->withCount('logs')
            ->get();

        $categories = Activity::whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return view('activities.index', compact('activities', 'categories'));
    }

    /**
     * Show create activity form (admin only).
     */
    public function create()
    {
        $categories = Activity::whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return view('activities.create', compact('categories'));
    }

    /**
     * Store a new activity (admin only).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category'    => 'nullable|string|max:100',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Activity::create($validated);

        return redirect()->route('activities.index')
            ->with('success', 'Activity created successfully.');
    }

    /**
     * Show edit form (admin only).
     */
    public function edit(Activity $activity)
    {
        $categories = Activity::whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return view('activities.edit', compact('activity', 'categories'));
    }

    /**
     * Update an activity (admin only).
     */
    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category'    => 'nullable|string|max:100',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $activity->update($validated);

        return redirect()->route('activities.index')
            ->with('success', 'Activity updated successfully.');
    }

    /**
     * Delete an activity (admin only).
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Activity deleted successfully.');
    }
}
