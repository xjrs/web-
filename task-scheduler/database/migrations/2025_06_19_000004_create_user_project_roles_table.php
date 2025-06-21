<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_project_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // 用户ID
            $table->unsignedBigInteger('project_id'); // 项目ID
            $table->string('role'); // 用户在项目中的角色（可以是预设角色或自定义角色）
            $table->boolean('is_manager')->default(false); // 是否为项目负责人
            $table->text('responsibilities')->nullable(); // 职责描述
            $table->timestamp('joined_at'); // 加入项目时间
            $table->timestamp('left_at')->nullable(); // 离开项目时间
            $table->timestamps();
            
            // 外键约束
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            
            // 唯一约束：同一用户在同一项目中只能有一个角色
            $table->unique(['user_id', 'project_id']);
            
            // 索引
            $table->index('user_id');
            $table->index('project_id');
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_project_roles');
    }
};