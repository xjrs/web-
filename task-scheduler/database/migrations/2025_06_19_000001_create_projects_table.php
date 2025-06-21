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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 项目名称
            $table->text('description')->nullable(); // 项目描述
            $table->unsignedBigInteger('manager_id'); // 项目经理ID
            $table->integer('participant_count')->default(0); // 项目参与人数
            $table->enum('status', ['planning', 'active', 'on_hold', 'completed', 'cancelled'])->default('planning'); // 项目状态
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium'); // 项目优先级
            $table->integer('total_tasks')->default(0); // 总任务数
            $table->timestamp('created_at')->useCurrent(); // 项目创建时间
            $table->timestamp('expected_start_time')->nullable(); // 预计开始时间
            $table->timestamp('actual_start_time')->nullable(); // 实际开始时间
            $table->timestamp('expected_end_time')->nullable(); // 预计结束时间
            $table->timestamp('actual_end_time')->nullable(); // 实际结束时间
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // 更新时间
            
            // 外键约束
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('cascade');
            
            // 索引
            $table->index(['status', 'priority']);
            $table->index('manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};