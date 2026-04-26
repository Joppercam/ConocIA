<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EditorialAgentTask;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class EditorialAgentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        if (!Schema::hasTable('editorial_agent_tasks')) {
            return view('admin.editorial-agent.index', [
                'tasks' => new LengthAwarePaginator([], 0, 20),
                'counts' => ['pending' => 0, 'approved' => 0, 'completed' => 0, 'rejected' => 0],
                'types' => collect(),
                'status' => $status,
                'tableReady' => false,
            ]);
        }

        $tasks = EditorialAgentTask::query()
            ->when($status !== 'all', fn($query) => $query->where('status', $status))
            ->when($request->filled('type'), fn($query) => $query->where('task_type', $request->input('type')))
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END")
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'pending' => EditorialAgentTask::where('status', 'pending')->count(),
            'approved' => EditorialAgentTask::where('status', 'approved')->count(),
            'completed' => EditorialAgentTask::where('status', 'completed')->count(),
            'rejected' => EditorialAgentTask::where('status', 'rejected')->count(),
        ];

        $types = EditorialAgentTask::query()
            ->select('task_type')
            ->distinct()
            ->orderBy('task_type')
            ->pluck('task_type');

        return view('admin.editorial-agent.index', compact('tasks', 'counts', 'types', 'status') + ['tableReady' => true]);
    }

    public function show(EditorialAgentTask $task)
    {
        return view('admin.editorial-agent.show', compact('task'));
    }

    public function approve(Request $request, EditorialAgentTask $task)
    {
        $task->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_notes' => $request->input('review_notes'),
        ]);

        return redirect()->route('admin.editorial-agent.index')->with('success', 'Propuesta aprobada. Quedó lista para ejecutar.');
    }

    public function complete(Request $request, EditorialAgentTask $task)
    {
        $task->update([
            'status' => 'completed',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_notes' => $request->input('review_notes', $task->review_notes),
        ]);

        return redirect()->route('admin.editorial-agent.index', ['status' => 'approved'])->with('success', 'Tarea marcada como ejecutada.');
    }

    public function reject(Request $request, EditorialAgentTask $task)
    {
        $task->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_notes' => $request->input('review_notes'),
        ]);

        return redirect()->route('admin.editorial-agent.index')->with('success', 'Propuesta descartada.');
    }
}
