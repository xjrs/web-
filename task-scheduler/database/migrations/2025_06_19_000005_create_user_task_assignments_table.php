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
        Schema::create('user_task_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // 用户ID
            $table->unsignedBigInteger('task_id'); // 任务ID
            $table->string('role')->default('assignee'); // 角色（支持预设角色和自定义角色）
            $table->boolean('is_manager')->default(false); // 是否为任务负责人
            $table->text('work_description')->nullable(); // 承担的工作描述
            $table->timestamp('assigned_at'); // 分配时间

            $table->timestamps();
            
            // 外键约束
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            
            // 索引
            $table->index('user_id');
            $table->index('task_id');
            $table->index('role');
            $table->index('assigned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_task_assignments');
    }
};