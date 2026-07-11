<?php

namespace Tests\Unit;

use App\DTO\CommentData;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Services\CommentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Task $task;

    protected CommentService $commentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->task = Task::factory()->create([
            'creator_id' => $this->user->id,
            'assignee_id' => $this->user->id,
        ]);
        $this->commentService = app(CommentService::class);
    }

    /** @test */
    public function can_create_comment(): void
    {
        $commentData = new CommentData(
            userId: $this->user->id,
            body: 'Тестовый комментарий',
            commentableType: Task::class,
            commentableId: $this->task->id,
        );

        $comment = $this->commentService->createComment($commentData);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'user_id' => $this->user->id,
            'body' => 'Тестовый комментарий',
            'commentable_id' => $this->task->id,
            'commentable_type' => Task::class,
        ]);
    }

    /** @test */
    public function can_update_comment(): void
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_id' => $this->task->id,
            'commentable_type' => Task::class,
        ]);

        $updatedComment = $this->commentService->updateComment(
            $comment,
            'Обновлённый текст',
            $this->user->id
        );

        $this->assertEquals('Обновлённый текст', $updatedComment->body);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'body' => 'Обновлённый текст',
        ]);
    }

    /** @test */
    public function can_delete_comment(): void
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_id' => $this->task->id,
            'commentable_type' => Task::class,
        ]);

        $this->commentService->deleteComment($comment, $this->user->id);

        $this->assertSoftDeleted('comments', [
            'id' => $comment->id,
        ]);
    }

    /** @test */
    public function can_get_comments_for_model(): void
    {
        Comment::factory()->count(3)->create([
            'commentable_id' => $this->task->id,
            'commentable_type' => Task::class,
        ]);

        $comments = $this->commentService->getCommentsForModel(Task::class, $this->task->id);

        $this->assertCount(3, $comments);
        $this->assertEquals(Task::class, $comments->first()->commentable_type);
    }

    /** @test */
    public function comments_are_ordered_by_latest_first(): void
    {
        $comment1 = Comment::factory()->create([
            'commentable_id' => $this->task->id,
            'commentable_type' => Task::class,
            'created_at' => now()->subDays(2),
        ]);
        $comment2 = Comment::factory()->create([
            'commentable_id' => $this->task->id,
            'commentable_type' => Task::class,
            'created_at' => now()->subDay(),
        ]);
        $comment3 = Comment::factory()->create([
            'commentable_id' => $this->task->id,
            'commentable_type' => Task::class,
            'created_at' => now(),
        ]);

        $comments = $this->commentService->getCommentsForModel(Task::class, $this->task->id);

        $this->assertEquals($comment3->id, $comments->first()->id);
        $this->assertEquals($comment1->id, $comments->last()->id);
    }
}
