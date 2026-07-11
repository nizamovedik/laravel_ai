<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private CommentService $commentService
    ) {}

    /**
     * Получить комментарии для модели
     */
    public function index(string $type, int $id): AnonymousResourceCollection
    {
        $comments = $this->commentService->getCommentsForModel($type, $id);

        return CommentResource::collection($comments);
    }

    /**
     * Создать комментарий
     */
    public function store(StoreCommentRequest $request, string $type, int $id): CommentResource
    {
        $this->authorize('create', Comment::class);

        $commentData = $request->toCommentData($type, $id);
        $comment = $this->commentService->createComment($commentData);

        return new CommentResource($comment->load('user'));
    }

    /**
     * Обновить комментарий
     */
    public function update(UpdateCommentRequest $request, Comment $comment): CommentResource
    {
        $this->authorize('update', $comment);

        $updatedComment = $this->commentService->updateComment(
            $comment,
            $request->input('body'),
            (int) $request->user()->id
        );

        return new CommentResource($updatedComment->load('user'));
    }

    /**
     * Удалить комментарий
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $this->commentService->deleteComment(
            $comment,
            (int) request()->user()->id
        );

        return response()->json(['message' => 'Комментарий удалён'], 200);
    }
}
