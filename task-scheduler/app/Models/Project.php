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
        'start_date',
        'end_date',
        'budget',
        'actual_cost',
        'completion_percentage',
        'is_archived'
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'completion_percentage' => 'integer',
        'is_archived' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
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
    public function managers()
    {
        return $this->users()->wherePivot('role', 'manager');
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
        if (!$this->end_date) {
            return false;
        }
        
        return now()->gt($this->end_date) && $this->status !== 'completed';
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