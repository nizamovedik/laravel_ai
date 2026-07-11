<?php

namespace App\Repositories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

class CommentRepository
{
    public function create(array $data): Comment
    {
        return Comment::create($data);
    }

    public function findById(int $id): ?Comment
    {
        return Comment::find($id);
    }

    public function getByCommentable(string $type, int $id): Collection
    {
        return Comment::where('commentable_type', $type)
            ->where('commentable_id', $id)
            ->with('user')
            ->latest()
            ->get();
    }

    public function update(Comment $comment, array $data): bool
    {
        return $comment->update($data);
    }

    public function delete(Comment $comment): bool
    {
        return $comment->delete($comment);
    }
}
