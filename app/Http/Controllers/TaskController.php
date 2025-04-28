<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = Task::query()
            ->with('user')
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        if ($request->wantsJson()) {
            return TaskResource::collection($tasks);
        }

        return Inertia::render('Tasks/Index', [
            'tasks' => TaskResource::collection($tasks),
            'filters' => $request->only('status'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Tasks/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|in:pending,in_progress,completed'
            ]);

            $task = Task::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'status' => $validated['status']
            ]);

            Log::info('Task created', ['task_id' => $task->id]);

            if ($request->wantsJson()) {
                return response()->json(new TaskResource($task), 201);
            }

            return redirect()->route('dashboard')->with('success', 'Task created successfully');
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Task creation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Failed to create task'], 500);
            }

            return back()->withErrors(['error' => 'Failed to create task']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        return Inertia::render('Tasks/Edit', [
            'task' => new TaskResource($task),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        try {
            $this->authorize('update', $task);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|in:pending,in_progress,completed'
            ]);

            $task->update($validated);

            Log::info('Task updated', ['task_id' => $task->id]);

            if ($request->wantsJson()) {
                return response()->json(new TaskResource($task));
            }

            return redirect()->route('dashboard')->with('success', 'Task updated successfully');
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Task update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Failed to update task'], 403);
            }

            return back()->withErrors(['error' => 'Failed to update task']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Task $task)
    {
        try {
            $this->authorize('delete', $task);

            $task->delete();

            Log::info('Task deleted', ['task_id' => $task->id]);

            if ($request->wantsJson()) {
                return response()->json(null, 200);
            }

            return redirect()->route('dashboard')->with('success', 'Task deleted successfully');
        } catch (\Exception $e) {
            Log::error('Task deletion error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Failed to delete task'], 403);
            }

            return back()->withErrors(['error' => 'Failed to delete task']);
        }
    }
}
