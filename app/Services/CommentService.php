<?php

namespace App\Services;

use App\DTO\CommentData;
use App\Models\Comment;
use App\Repositories\CommentRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentService
{
    public function __construct(
        private CommentRepository $commentRepository
    ) {}

    public function createComment(CommentData $data): Comment
    {
        return DB::transaction(function () use ($data) {
            $comment = $this->commentRepository->create([
                'user_id' => $data->userId,
                'body' => $data->body,
                'commentable_id' => $data->commentableId,
                'commentable_type' => $data->commentableType,
            ]);

            Log::info("Комментарий {$comment->id} создан пользователем {$data->userId}");

            return $comment;
        });
    }

    public function updateComment(Comment $comment, string $body, int $userId): Comment
    {
        $this->commentRepository->update($comment, ['body' => $body]);

        Log::info("Комментарий {$comment->id} обновлён пользователем {$userId}");

        return $comment->fresh();
    }

    public function deleteComment(Comment $comment, int $userId): void
    {
        $this->commentRepository->delete($comment);

        Log::info("Комментарий {$comment->id} удалён пользователем {$userId}");
    }

    public function getCommentsForModel(string $type, int $id): Collection
    {
        return $this->commentRepository->getByCommentable($type, $id);
    }
}
