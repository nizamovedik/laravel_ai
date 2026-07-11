<?php

namespace Tests\Feature;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskStatusTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected User $user;

    protected Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->task = Task::factory()->create([
            'status_id' => TaskStatusEnum::NEW->value,
            'creator_id' => $this->user->id,
            'assignee_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function authenticated_user_can_change_task_status(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$this->task->id}/status", [
                'status_id' => TaskStatusEnum::IN_PROGRESS->value,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Статус обновлён',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'status_id' => TaskStatusEnum::IN_PROGRESS->value,
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_change_task_status(): void
    {
        $response = $this->putJson("/api/tasks/{$this->task->id}/status", [
            'status_id' => TaskStatusEnum::IN_PROGRESS->value,
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function cannot_change_status_to_invalid_status(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$this->task->id}/status", [
                'status_id' => 999,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status_id']);
    }

    /** @test */
    public function cannot_transition_to_not_allowed_status(): void
    {
        $this->task->update(['status_id' => TaskStatusEnum::NEW->value]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$this->task->id}/status", [
                'status_id' => TaskStatusEnum::REVIEW->value,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'Невозможно перевести задачу из статуса \'Новая\' в \'На ревью\'',
            ]);
    }

    /** @test */
    public function task_history_is_logged_when_status_changes(): void
    {
        $this->actingAs($this->user)
            ->putJson("/api/tasks/{$this->task->id}/status", [
                'status_id' => TaskStatusEnum::IN_PROGRESS->value,
            ]);

        $this->assertDatabaseHas('task_histories', [
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'field' => 'status',
            'old_value' => 'Новая',
            'new_value' => 'В работе',
        ]);
    }

    /** @test */
    public function started_at_is_set_when_transitioning_to_in_progress(): void
    {
        $this->task->update(['started_at' => null]);

        $this->actingAs($this->user)
            ->putJson("/api/tasks/{$this->task->id}/status", [
                'status_id' => TaskStatusEnum::IN_PROGRESS->value,
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'started_at' => now(),
        ]);
    }

    /** @test */
    public function completed_at_is_set_when_transitioning_to_done(): void
    {
        $this->task->update([
            'status_id' => TaskStatusEnum::REVIEW->value,
            'completed_at' => null,
        ]);

        $this->actingAs($this->user)
            ->putJson("/api/tasks/{$this->task->id}/status", [
                'status_id' => TaskStatusEnum::DONE->value,
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'completed_at' => now(),
        ]);
    }

    /** @test */
    public function user_can_transition_task_from_on_hold_to_new(): void
    {
        $this->task->update(['status_id' => TaskStatusEnum::ON_HOLD->value]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$this->task->id}/status", [
                'status_id' => TaskStatusEnum::NEW->value,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'status_id' => TaskStatusEnum::NEW->value,
        ]);
    }

    /** @test */
    public function user_can_transition_task_from_done_back_to_review(): void
    {
        $this->task->update(['status_id' => TaskStatusEnum::DONE->value]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$this->task->id}/status", [
                'status_id' => TaskStatusEnum::REVIEW->value,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'status_id' => TaskStatusEnum::REVIEW->value,
        ]);
    }

    /** @test */
    public function user_cannot_transition_closed_task(): void
    {
        $this->task->update(['status_id' => TaskStatusEnum::CLOSED->value]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$this->task->id}/status", [
                'status_id' => TaskStatusEnum::NEW->value,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'Невозможно перевести задачу из статуса \'Закрыта\' в \'Новая\'',
            ]);
    }

    /** @test */
    public function user_can_transition_task_from_review_to_in_progress(): void
    {
        $this->task->update(['status_id' => TaskStatusEnum::REVIEW->value]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$this->task->id}/status", [
                'status_id' => TaskStatusEnum::IN_PROGRESS->value,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'status_id' => TaskStatusEnum::IN_PROGRESS->value,
        ]);
    }
}
