<?php

namespace App\Http\Requests\Article;

use APP\Enums\ArticleStatus;
use App\Services\ArticleService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(ArticleService $articleService): bool
    {
        return $this->user()->hasRole('writer') || $this->user()
                ->hasRole('admin');
    }

    protected function prepareForValidation(): void
    {
        $this->merge ([
            'title' => $this->title ? trim(ucfirst(strtolower($this->title))) : null,
            'description' => $this->description ? trim(ucfirst(strtolower($this->description))) : null,

        ]);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $articleId = $this->route('article')?->id ?? $this->route('article');
        return [
            'title' => ['sometimes', 'filled', 'string', 'min:10' , 'max:500',
                Rule::unique('articles', 'title')->ignore($articleId)],
            'description' => ['sometimes', 'filled', 'string', 'min:100', 'max:2000'],
            'tags' => ['sometimes', 'nullable', 'array'],
            'tags.*' => ['integer' ,'exists:tags,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.filled' => 'The article title cannot be empty.',
            'title.string' => 'The article title must be a string.',
            'title.min'    => 'The article title must be at least 10 characters.',
            'title.max'    => 'The article title must not exceed 500 characters.',
            'title.unique' => 'This article title already exists, please choose another.',
            'description.filled' => 'The article content cannot be empty.',
            'description.string' => 'The article content must be a string.',
            'description.min'    => 'The article content must be at least 100 characters.',
            'description.max'    => 'The article content must not exceed 2000 characters.',
            'tags.array'    => 'The tags must be an array.',
            'tags.*.exists' => 'One or more of the selected tags do not exist.',
        ];
    }
}
