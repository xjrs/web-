<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'description', 
        'status',
        'priority',
        'expected_start_time',
        'actual_start_time',
        'expected_end_time',
        'actual_end_time',
        'project_id',
        'manager_id'
    ];
    
    protected $casts = [
        'expected_start_time' => 'datetime',
        'actual_start_time' => 'datetime',
        'expected_end_time' => 'datetime',
        'actual_end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * 获取任务所属的项目
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * 获取任务负责人
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
    
    /**
     * 获取任务分配给的用户
     */
    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_task_assignments')
                    ->withPivot('role', 'work_description', 'assigned_at')
                    ->withTimestamps();
    }
    
    /**
     * 获取任务的用户分配记录
     */
    public function userAssignments(): HasMany
    {
        return $this->hasMany(UserTaskAssignment::class);
    }
    
    /**
     * 检查任务是否延期
     */
    public function isDelayed(): bool
    {
        if (!$this->expected_end_time) {
            return false;
        }
        
        return now()->gt($this->expected_end_time) && $this->status !== 'completed';
    }
    

}


