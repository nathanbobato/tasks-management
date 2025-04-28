<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $status = $request->input('status');
            $page = $request->input('page', 1);

            Log::info('Dashboard request', [
                'user_id' => $user->id,
                'status' => $status,
                'page' => $page,
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson(),
                'headers' => $request->headers->all()
            ]);

            $query = Task::where('user_id', $user->id)
                ->with('user')
                ->orderBy('created_at', 'desc');

            if ($status) {
                $query->where('status', $status);
            }

            $tasks = $query->paginate(10);

            Log::info('Tasks found', [
                'count' => $tasks->count(),
                'total' => $tasks->total(),
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage()
            ]);

            return Inertia::render('Dashboard', [
                'tasks' => $tasks,
                'filters' => [
                    'status' => $status
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}