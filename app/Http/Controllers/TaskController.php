<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller {
    
    // 1. List Tasks: Sorted by priority (high-low), then due_date ASC 
    public function index(Request $request) {
        $query = Task::query();

        // Optional status query parameter [cite: 50]
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
                       ->orderBy('due_date', 'asc')
                       ->get();

        // Return meaningful JSON if no tasks exist 
        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'No tasks found matching your criteria.'], 200);
        }

        return response()->json($tasks);
    }

    // 2. Create Task: Enforces date, priority, and uniqueness rules 
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'due_date' => 'required|date|after_or_equal:today', // Must be today or later [cite: 43]
            'priority' => 'required|in:low,medium,high',      // Valid enum check 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Rule: title cannot duplicate a task with the same due_date [cite: 41]
        $exists = Task::where('title', $request->title)
                      ->where('due_date', $request->due_date)
                      ->exists();

        if ($exists) {
            return response()->json(['error' => 'Duplicate task for this date'], 422);
        }

        return Task::create($request->all());
    }

    // 3. Update Status: Enforces linear progression (pending -> in_progress -> done) 
    public function updateStatus(Request $request, $id) {
        $task = Task::findOrFail($id);
        
        // Define allowed next steps [cite: 57]
        $allowed = [
            'pending' => 'in_progress',
            'in_progress' => 'done'
        ];

        // Cannot skip or revert status [cite: 59]
        if (!isset($allowed[$task->status]) || $allowed[$task->status] !== $request->status) {
            return response()->json(['error' => 'Invalid progression. Status must move Pending -> In Progress -> Done.'], 422);
        }

        $task->update(['status' => $request->status]);
        return response()->json($task);
    }

    // 4. Delete Task: Only allowed if status is 'done' 
    public function destroy($id) {
        $task = Task::findOrFail($id);

        if ($task->status !== 'done') {
            return response()->json(['error' => 'Only done tasks can be deleted'], 403); // 403 Forbidden [cite: 65]
        }

        $task->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // 5. Bonus: Daily Report 
    public function report(Request $request) {
        $date = $request->query('date', date('Y-m-d')); // Defaults to today if no date provided 
        
        $priorities = ['high', 'medium', 'low'];
        $statuses = ['pending', 'in_progress', 'done'];
        $report = [
            'date' => $date,
            'summary' => []
        ];

        foreach ($priorities as $p) {
            foreach ($statuses as $s) {
                // Count tasks per priority and status for the specific day 
                $report['summary'][$p][$s] = Task::where('due_date', $date)
                    ->where('priority', $p)
                    ->where('status', $s)
                    ->count();
            }
        }

        return response()->json($report); // Returns structured JSON 
    }
}