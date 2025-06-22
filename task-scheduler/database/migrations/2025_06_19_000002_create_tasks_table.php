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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // 任务标题
            $table->text('description')->nullable(); // 任务描述
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending'); // 任务状态
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium'); // 任务优先级
            $table->timestamp('created_at')->useCurrent(); // 任务创建时间
            $table->timestamp('expected_start_time')->nullable(); // 预计开始时间
            $table->timestamp('actual_start_time')->nullable(); // 实际开始时间
            $table->timestamp('expected_end_time')->nullable(); // 预计结束时间
            $table->timestamp('actual_end_time')->nullable(); // 实际结束时间
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // 更新时间
            $table->unsignedBigInteger('project_id')->nullable(); // 所属项目ID（可选）
            $table->unsignedBigInteger('manager_id')->nullable(); // 任务负责人ID
            
            // 外键约束
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');

            
            // 索引
            $table->index(['status', 'priority']);
            $table->index('project_id');
            $table->index(['expected_start_time', 'expected_end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};