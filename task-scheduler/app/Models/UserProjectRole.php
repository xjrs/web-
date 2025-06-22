<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProjectRole extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'project_id',
        'role',
        'joined_at'
    ];
    
    protected $casts = [
        'joined_at' => 'datetime',
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
     * 获取关联的项目
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    
    /**
     * 检查是否为管理员
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }
    
    /**
     * 检查是否为成员
     */
    public function isMember(): bool
    {
        return $this->role === 'member';
    }
}