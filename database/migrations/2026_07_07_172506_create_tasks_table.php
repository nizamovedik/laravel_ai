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
            $table->string('title');
            $table->text('description')->nullable();

            // Внешние ключи
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('task_statuses');
            $table->foreignId('priority_id')->nullable()->constrained('task_priorities')->nullOnDelete();
            $table->foreignId('creator_id')->constrained('users');
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();

            // Даты
            $table->timestamp('deadline_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Оценка времени
            $table->decimal('estimated_hours', 5, 2)->nullable();

            $table->timestamps();

            $table->index(['project_id', 'status_id']);
            $table->index(['assignee_id', 'status_id']);
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
