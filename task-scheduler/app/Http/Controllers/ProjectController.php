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
        $query = Project::with(['users', 'tasks']);
        
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
        
        // 是否包含已归档项目
        if (!$request->boolean('include_archived')) {
            $query->where('is_archived', false);
        }
        
        $projects = $query->get();
        
        // stats 属性通过访问器自动计算，无需手动赋值
        
        return response()->json([
            'success' => true,
            'data' => $projects,
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
                $query->with('assignedUsers');
            }
        ]);
        
        $project->stats = $project->stats;
        
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
            'status' => ['required', Rule::in(['planning', 'active', 'paused', 'completed', 'cancelled'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
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
            'status' => ['sometimes', Rule::in(['planning', 'active', 'paused', 'completed', 'cancelled'])],
            'priority' => ['sometimes', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'completion_percentage' => 'nullable|integer|min:0|max:100',
            'is_archived' => 'nullable|boolean'
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
        $total = $projects->count();
        $active = $projects->where('status', 'active')->count();
        $completed = $projects->where('status', 'completed')->count();
        $planning = $projects->where('status', 'planning')->count();
        $delayed = $projects->filter(function ($project) {
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