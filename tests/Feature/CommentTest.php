<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->task = Task::factory()->create([
            'creator_id' => $this->user->id,
            'assignee_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function authenticated_user_can_get_comments(): void
    {
        Comment::factory()->count(3)->create([
            'commentable_id' => $this->task->id,
            'commentable_type' => 'task',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/comments/task/{$this->task->id}");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function unauthenticated_user_cannot_get_comments(): void
    {
        $response = $this->getJson("/api/comments/task/{$this->task->id}");

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_create_comment(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/comments/task/{$this->task->id}", [
                'body' => 'Новый комментарий',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.body', 'Новый комментарий');

        $this->assertDatabaseHas('comments', [
            'body' => 'Новый комментарий',
            'user_id' => $this->user->id,
            'commentable_id' => $this->task->id,
            'commentable_type' => 'task',
        ]);
    }

    /** @test */
    public function comment_requires_body(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/comments/task/{$this->task->id}", [
                'body' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['body']);
    }

    /** @test */
    public function user_can_update_own_comment(): void
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_id' => $this->task->id,
            'commentable_type' => 'task',
            'body' => 'Старый текст',
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/comments/{$comment->id}", [
                'body' => 'Новый текст',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.body', 'Новый текст');

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'body' => 'Новый текст',
        ]);
    }

    /** @test */
    public function user_cannot_update_others_comment(): void
    {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $otherUser->id,
            'commentable_id' => $this->task->id,
            'commentable_type' => 'task',
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/comments/{$comment->id}", [
                'body' => 'Попытка взлома',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_delete_own_comment(): void
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_id' => $this->task->id,
            'commentable_type' => 'task',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/comments/{$comment->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Комментарий удалён']);

        $this->assertSoftDeleted('comments', [
            'id' => $comment->id,
        ]);
    }

    /** @test */
    public function user_cannot_delete_others_comment(): void
    {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $otherUser->id,
            'commentable_id' => $this->task->id,
            'commentable_type' => 'task',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/comments/{$comment->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function comment_response_contains_can_update_and_can_delete_flags(): void
    {
        Comment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_id' => $this->task->id,
            'commentable_type' => 'task',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/comments/task/{$this->task->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.0.can_update', true)
            ->assertJsonPath('data.0.can_delete', true);
    }

    /** @test */
    public function can_create_comment_to_project(): void
    {
        $project = Project::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/comments/project/{$project->id}", [
                'body' => 'Комментарий к проекту',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('comments', [
            'body' => 'Комментарий к проекту',
            'user_id' => $this->user->id,
            'commentable_id' => $project->id,
            'commentable_type' => 'project',
        ]);
    }

    /** @test */
    public function authenticated_user_can_get_comments_for_project(): void
    {
        $project = Project::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        Comment::factory()->count(3)->create([
            'commentable_id' => $project->id,
            'commentable_type' => 'project', // или Project::class, зависит от морф-карты
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/comments/project/{$project->id}");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function authenticated_user_can_create_comment_for_project(): void
    {
        $project = Project::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/comments/project/{$project->id}", [
                'body' => 'Комментарий к проекту',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.body', 'Комментарий к проекту');

        $this->assertDatabaseHas('comments', [
            'body' => 'Комментарий к проекту',
            'user_id' => $this->user->id,
            'commentable_id' => $project->id,
            'commentable_type' => 'project',
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_comment_for_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->postJson("/api/comments/project/{$project->id}", [
            'body' => 'Комментарий к проекту',
        ]);

        $response->assertStatus(401);
    }
}
