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
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id'); // 当前任务ID
            $table->unsignedBigInteger('depends_on_task_id'); // 依赖的任务ID
            $table->enum('dependency_type', ['finish_to_start', 'start_to_start', 'finish_to_finish', 'start_to_finish'])->default('finish_to_start'); // 依赖类型
            $table->integer('lag_days')->default(0); // 滞后天数（可以为负数表示提前）
            $table->text('description')->nullable(); // 依赖关系描述
            $table->timestamps();
            
            // 外键约束
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('depends_on_task_id')->references('id')->on('tasks')->onDelete('cascade');
            
            // 唯一约束：防止重复的依赖关系
            $table->unique(['task_id', 'depends_on_task_id']);
            
            // 索引
            $table->index('task_id');
            $table->index('depends_on_task_id');
            $table->index('dependency_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_dependencies');
    }
};