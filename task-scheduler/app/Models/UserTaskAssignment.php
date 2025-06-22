<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTaskAssignment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'task_id',
        'role',
        'is_manager',
        'work_description',
        'assigned_at'
    ];
    
    protected $casts = [
        'is_manager' => 'boolean',
        'assigned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * 获取关联的用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * 获取关联的任务
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
    
    /**
     * 检查是否为任务管理员
     */
    public function isTaskManager(): bool
    {
        return $this->is_manager;
    }
}