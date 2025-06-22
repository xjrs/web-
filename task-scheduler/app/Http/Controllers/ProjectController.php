<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\UserProjectRole;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * 获取项目列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = Project::with(['users', 'tasks', 'manager']);
        
        // 如果用户已登录，只显示用户参与的项目
        if (Auth::check()) {
            $userId = Auth::id();
            $query->whereHas('users', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }
        
        // 状态筛选
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // 优先级筛选
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }
        
        // 搜索
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // 是否包含已取消项目
        if (!$request->boolean('include_cancelled')) {
            $query->where('status', '!=', 'cancelled');
        }
        
        // 分页参数
        $perPage = $request->get('per_page', 15);
        $projects = $query->paginate($perPage);
        
        // 调试日志：记录返回的项目数据
        \Illuminate\Support\Facades\Log::info('ProjectController@index - Projects data:', [
            'total_projects' => $projects->total(),
            'current_page' => $projects->currentPage(),
            'projects_count' => count($projects->items())
        ]);
        
        // 调试日志：记录每个项目的stats数据
        foreach ($projects->items() as $project) {
            \Illuminate\Support\Facades\Log::info('Project stats for ID ' . $project->id . ':', [
                'stats' => $project->stats
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => $projects->items(),
            'pagination' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total()
            ],
            'stats' => $this->getProjectStats($projects)
        ]);
    }
    
    /**
     * 获取项目详情
     */
    public function show(Project $project): JsonResponse
    {
        // 检查用户权限（如果已登录）
        if (Auth::check()) {
            $userId = Auth::id();
            $hasAccess = $project->users()->where('user_id', $userId)->exists();
            
            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => '您没有权限查看此项目'
                ], 403);
            }
        }
        
        $project->load([
            'users' => function ($query) {
                $query->withPivot('role', 'joined_at');
            },
            'tasks' => function ($query) {
                $query->with(['assignedUsers' => function ($q) {
                    $q->select('users.id', 'name', 'email')
                      ->withPivot('role', 'work_description', 'assigned_at');
                }]);
            }
        ]);
        
        // 调试日志：记录项目基本信息
        \Illuminate\Support\Facades\Log::info('ProjectController@show - Project loaded:', [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'tasks_count' => $project->tasks->count(),
            'users_count' => $project->users->count()
        ]);
        
        // 调试日志：记录项目stats数据
        \Illuminate\Support\Facades\Log::info('ProjectController@show - Project stats:', [
            'project_id' => $project->id,
            'stats' => $project->stats
        ]);
        
        // 调试日志：记录任务详情
        foreach ($project->tasks as $task) {
            \Illuminate\Support\Facades\Log::info('Task details for project ' . $project->id . ':', [
                'task_id' => $task->id,
                'task_title' => $task->title,
                'task_status' => $task->status,
                'assigned_users_count' => $task->assignedUsers->count()
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => $project
        ]);
    }
    
    /**
     * 创建新项目
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['planning', 'active', 'on_hold', 'completed', 'cancelled'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'expected_start_time' => 'nullable|date',
            'expected_end_time' => 'nullable|date|after_or_equal:expected_start_time',
            'actual_start_time' => 'nullable|date',
            'actual_end_time' => 'nullable|date',
            'manager_id' => 'required|exists:users,id',
            'participant_count' => 'nullable|integer|min:0',
            'total_tasks' => 'nullable|integer|min:0'
        ]);
        
        DB::beginTransaction();
        
        try {
            $project = Project::create($request->all());
            
            // 如果用户已登录，将其设为项目管理员
            if (Auth::check()) {
                UserProjectRole::create([
                    'user_id' => Auth::id(),
                    'project_id' => $project->id,
                    'role' => 'manager',
                    'joined_at' => now()
                ]);
            }
            
            DB::commit();
            
            $project->load(['users', 'tasks']);
            $project->stats = $project->stats;
            
            return response()->json([
                'success' => true,
                'data' => $project,
                'message' => '项目创建成功'
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => '项目创建失败：' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 更新项目
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        // 检查用户权限
        if (Auth::check()) {
            $userId = Auth::id();
            $isManager = $project->users()
                ->where('user_id', $userId)
                ->wherePivot('role', 'manager')
                ->exists();
            
            if (!$isManager) {
                return response()->json([
                    'success' => false,
                    'message' => '您没有权限修改此项目'
                ], 403);
            }
        }
        
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['sometimes', Rule::in(['planning', 'active', 'on_hold', 'completed', 'cancelled'])],
            'priority' => ['sometimes', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'expected_start_time' => 'nullable|date',
            'expected_end_time' => 'nullable|date|after_or_equal:expected_start_time',
            'actual_start_time' => 'nullable|date',
            'actual_end_time' => 'nullable|date',
            'manager_id' => 'sometimes|required|exists:users,id',
            'participant_count' => 'nullable|integer|min:0',
            'total_tasks' => 'nullable|integer|min:0'
        ]);
        
        $project->update($request->all());
        
        $project->load(['users', 'tasks']);
        $project->stats = $project->stats;
        
        return response()->json([
            'success' => true,
            'data' => $project,
            'message' => '项目更新成功'
        ]);
    }
    
    /**
     * 删除项目
     */
    public function destroy(Project $project): JsonResponse
    {
        // 检查用户权限
        if (Auth::check()) {
            $userId = Auth::id();
            $isManager = $project->users()
                ->where('user_id', $userId)
                ->wherePivot('role', 'manager')
                ->exists();
            
            if (!$isManager) {
                return response()->json([
                    'success' => false,
                    'message' => '您没有权限删除此项目'
                ], 403);
            }
        }
        
        $project->delete();
        
        return response()->json([
            'success' => true,
            'message' => '项目删除成功'
        ]);
    }
    
    /**
     * 获取项目统计信息
     */
    public function stats(): JsonResponse
    {
        $query = Project::query();
        
        // 如果用户已登录，只统计用户参与的项目
        if (Auth::check()) {
            $userId = Auth::id();
            $query->whereHas('users', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }
        
        $projects = $query->with(['tasks'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $this->getProjectStats($projects)
        ]);
    }
    

    
    /**
     * 计算项目统计信息
     */
    private function getProjectStats($projects): array
    {
        $collection = $projects->getCollection();
        $total = $collection->count();
        $active = $collection->where('status', 'active')->count();
        $completed = $collection->where('status', 'completed')->count();
        $planning = $collection->where('status', 'planning')->count();
        $delayed = $collection->filter(function ($project) {
            return $project->isDelayed();
        })->count();
        
        return [
            'total' => $total,
            'active' => $active,
            'completed' => $completed,
            'planning' => $planning,
            'delayed' => $delayed
        ];
    }
}