<?php

namespace App\Http\Controllers;

use App\Http\Requests\Article\StoreArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleService $articleService){}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $articles = $this->articleService->getAllArticles($user);
        return $this->successResponse(
            data: ['articles' => $articles],
            message: "Get all articles"
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        $data = $request->validated();
        $article = $this->articleService->create($data);
        return $this->successResponse(
            data: ['article' => $article],
            message: "Created article successfully .",
            code: 201,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $article = $this->articleService->getArticleById($id);
        return $this->successResponse(
            data: ['article' => $article],
            message: "Get article successfully.",
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, int $id)
    {
        $article = $this->articleService->getArticleById($id);
        $this->authorize('update', $article);
        $updated = $this->articleService->update($id, $request->validated());
        return $this->successResponse(
            data: ['article' => $updated],
            message: 'Article updated successfully.',
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $article = $this->articleService->getArticleById($id);
        $this->authorize('delete', $article);
        $this->articleService->delete($id);
        return $this->successResponse(
            message: 'Article deleted successfully.',
        );
    }

    public function publish(Request $request, int $id)
    {
        $article = $this->articleService->publish($id);
        $this->authorize('publish', $article);
        return response()->json(['message' => 'Article published successfully', 'article' => $article]);
    }

    public function archive(Request $request, int $id)
    {
        $article = $this->articleService->archive($id);
        $this->authorize('archive', $article);
        return response()->json(['message' => 'Article archived successfully', 'article' => $article]);
    }

}
