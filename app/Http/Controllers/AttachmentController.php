<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\ArticleService;
use App\Services\AttachmentService;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function __construct(private readonly AttachmentService $service)
    {}

    public function upload(Request $request, Article $article)
    {
         $request->validate([
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);
        $attachment = $this->service->store($article, $request->file('attachment'));
        return $this->successResponse(
            data: $attachment,
            message: 'File uploaded successfully'
        );

    }
}
