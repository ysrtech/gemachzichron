<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query()->with(['causer', 'subject']);

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('subject_type', $request->model_type);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $activities = $query->latest()->paginate(20);

        // Get all users for filter dropdown
        $users = User::orderBy('name')->get(['id', 'name']);

        // Get unique model types from activity log
        $modelTypes = Activity::select('subject_type')
            ->distinct()
            ->whereNotNull('subject_type')
            ->pluck('subject_type')
            ->map(function ($type) {
                return [
                    'value' => $type,
                    'label' => class_basename($type),
                ];
            })
            ->sortBy('label')
            ->values();

        return Inertia::render('ActivityLogs/Index', [
            'filters' => $request->all(['user_id', 'model_type', 'from_date', 'to_date']),
            'activities' => $activities,
            'users' => $users,
            'modelTypes' => $modelTypes,
        ]);
    }
}
