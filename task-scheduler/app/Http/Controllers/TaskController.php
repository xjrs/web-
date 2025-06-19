<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // 获取所有任务
    public function index()
    {
        return Task::all();
    }

    // 创建新任务
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $task = Task::create($request->all());

        return response()->json($task, 201);
    }

    // 获取单个任务
    public function show(Task $task)
    {
        return $task;
    }

    // 更新任务
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $task->update($request->all());

        return response()->json($task, 200);
    }

    // 删除任务
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(null, 204);
    }
}
