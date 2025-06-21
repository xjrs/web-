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
        Schema::create('project_task_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id'); // 项目ID
            $table->unsignedBigInteger('task_id'); // 任务ID
            $table->boolean('is_critical')->default(false); // 是否为关键任务

            $table->integer('dependency_level')->default(0); // 依赖级别（0表示无依赖，数字越大依赖越多）
            $table->decimal('completion_weight', 5, 2)->default(1.00); // 任务在项目中的完成权重
            $table->text('impact_description')->nullable(); // 任务对项目的影响描述
            $table->timestamps();
            
            // 外键约束
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            
            // 唯一约束：一个任务在一个项目中只能有一个关系记录
            $table->unique(['project_id', 'task_id']);
            
            // 索引
            $table->index('project_id');
            $table->index('task_id');
            $table->index('is_critical');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_task_relations');
    }
};