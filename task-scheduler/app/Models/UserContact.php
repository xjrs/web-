<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contact_user_id',
        'contact_name',
        'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 获取拥有此联系人的用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 获取联系人用户信息
     */
    public function contactUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contact_user_id');
    }

    /**
     * 获取联系人显示名称（优先使用备注名称，否则使用用户真实姓名）
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->contact_name ?: $this->contactUser->name;
    }

    /**
     * 获取联系人邮箱
     */
    public function getContactEmailAttribute(): string
    {
        return $this->contactUser->email;
    }

    /**
     * 获取联系人头像
     */
    public function getContactAvatarAttribute(): string
    {
        return $this->contactUser->avatar ?: 'default-avatar.png';
    }
}