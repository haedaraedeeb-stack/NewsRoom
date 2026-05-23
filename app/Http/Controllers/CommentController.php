<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Article;
use App\Services\ArticleService;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService,
        private readonly ArticleService $articleService,
    ) {}

    public function index(int $articleId)
    {
        $comments = $this->commentService->getComments(Article::class, $articleId);
        return $this->successResponse(
            data: ['comments' => $comments],
            message: 'Comments retrieved successfully.',
        );
    }

    public function store(StoreCommentRequest $request, int $articleId)
    {
        $article = $this->articleService->getArticleById($articleId);
        $comment = $this->commentService->create(
            article: $article,
            userId: $request->user()->id,
            data: $request->validated(),
        );
        return $this->successResponse(
            data: ['comment' => $comment],
            message: 'Comment created successfully.',
            code: 201,
        );
    }

    public function update(UpdateCommentRequest $request, int $id)
    {
        $comment = $this->commentService->findById($id);
        $this->authorize('update', $comment);
        $updated = $this->commentService->update($id, $request->validated());
        return $this->successResponse(
            data: ['comment' => $updated],
            message: 'Comment updated successfully.',
        );
    }

    public function destroy(int $id)
    {
        $comment = $this->commentService->findById($id);
        $this->authorize('delete', $comment);
        $this->commentService->delete($id);
        return $this->successResponse(
            message: 'Comment deleted successfully.',
        );
    }
}
