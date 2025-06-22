<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property array $stats 项目统计信息
 */

class Project extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'status',
        'priority',
        'expected_start_time',
        'expected_end_time',
        'actual_start_time',
        'actual_end_time',
        'manager_id',
        'participant_count',
        'total_tasks'
    ];
    
    protected $casts = [
        'expected_start_time' => 'datetime',
        'expected_end_time' => 'datetime',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
        'participant_count' => 'integer',
        'total_tasks' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * 自动追加的属性
     */
    protected $appends = ['stats'];
    
    /**
     * 获取项目的所有任务
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
    
    /**
     * 获取项目的用户角色关系
     */
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserProjectRole::class);
    }
    
    /**
     * 获取项目的用户（通过角色关系）
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_project_roles')
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }
    
    /**
     * 获取项目管理员
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
    
    /**
     * 获取项目成员
     */
    public function members()
    {
        return $this->users()->wherePivot('role', 'member');
    }
    
    /**
     * 计算项目进度
     */
    public function calculateProgress()
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            return 0;
        }
        
        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        return round(($completedTasks / $totalTasks) * 100);
    }
    
    /**
     * 检查项目是否延期
     */
    public function isDelayed()
    {
        if (!$this->expected_end_time) {
            return false;
        }
        
        return now()->gt($this->expected_end_time) && $this->status !== 'completed';
    }
    
    /**
     * 获取项目统计信息
     */
    /**
     * 获取项目经理信息
     */
    public function getManagerAttribute()
    {
        return $this->manager;
    }

    /**
     * 获取团队成员信息
     */
    public function getTeamMembersAttribute()
    {
        return $this->users()
            ->wherePivot('role', 'member')
            ->get();
    }

    /**
     * 获取项目统计信息
     */
    public function getStatsAttribute()
    {
        return [
            'total_tasks' => $this->tasks()->count(),
            'completed_tasks' => $this->tasks()->where('status', 'completed')->count(),
            'in_progress_tasks' => $this->tasks()->where('status', 'in_progress')->count(),
            'pending_tasks' => $this->tasks()->where('status', 'pending')->count(),
            'team_size' => $this->users()->count(),
            'progress' => $this->calculateProgress(),
            'is_delayed' => $this->isDelayed()
        ];
    }
}