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
        Schema::create('user_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // 用户ID
            $table->unsignedBigInteger('contact_user_id'); // 联系人用户ID
            $table->string('contact_name')->nullable(); // 联系人备注名称
            $table->text('notes')->nullable(); // 备注信息
            $table->timestamps();
            
            // 外键约束
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('contact_user_id')->references('id')->on('users')->onDelete('cascade');
            
            // 唯一约束：同一用户不能重复添加同一联系人
            $table->unique(['user_id', 'contact_user_id']);
            
            // 索引
            $table->index('user_id');
            $table->index('contact_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_contacts');
    }
};