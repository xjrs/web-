<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * 获取用户参与的项目
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'user_project_roles')
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }
    
    /**
     * 获取用户的项目角色
     */
    public function projectRoles(): HasMany
    {
        return $this->hasMany(UserProjectRole::class);
    }
    
    /**
     * 获取用户分配的任务
     */
    public function assignedTasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'user_task_assignments')
                    ->withPivot('role', 'is_manager', 'work_description', 'assigned_at')
                    ->withTimestamps();
    }
    
    /**
     * 获取用户的任务分配记录
     */
    public function taskAssignments(): HasMany
    {
        return $this->hasMany(UserTaskAssignment::class);
    }
    
    /**
     * 获取用户管理的项目
     */
    public function managedProjects()
    {
        return $this->projects()->wherePivot('role', 'manager');
    }
    
    /**
     * 获取用户作为成员的项目
     */
    public function memberProjects()
    {
        return $this->projects()->wherePivot('role', 'member');
    }
    
    /**
     * 检查用户是否为指定项目的管理员
     */
    public function isProjectManager(Project $project): bool
    {
        return $this->projects()
                    ->where('project_id', $project->id)
                    ->wherePivot('role', 'manager')
                    ->exists();
    }
    
    /**
     * 检查用户是否参与指定项目
     */
    public function isInProject(Project $project): bool
    {
        return $this->projects()->where('project_id', $project->id)->exists();
    }
    
    /**
     * 获取用户的通讯录联系人
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(UserContact::class, 'user_id');
    }
    
    /**
     * 获取用户的通讯录联系人（包含联系人用户信息）
     */
    public function contactsWithUser(): HasMany
    {
        return $this->hasMany(UserContact::class, 'user_id')
                    ->with('contactUser');
    }
    
    /**
     * 获取将此用户作为联系人的记录
     */
    public function contactedBy(): HasMany
    {
        return $this->hasMany(UserContact::class, 'contact_user_id');
    }
}
