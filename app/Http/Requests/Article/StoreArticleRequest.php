<?php

namespace App\Http\Requests\Article;

use App\Enums\ArticleStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('writer') || $this->user()
            ->hasRole('admin');
    }

    protected function prepareForValidation(): void
    {
        $this->merge ([
            'title' => trim(ucfirst(strtolower($this->title))),
            'description' => trim(ucfirst(strtolower($this->description))),

        ]);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:10' , 'max:500', 'unique:articles,title'],
            'description' => ['required', 'string', 'min:100', 'max:2000'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The article title is required.',
            'title.string'   => 'The article title must be a string.',
            'title.min'      => 'The article title must be at least 10 characters.',
            'title.max'      => 'The article title must not exceed 500 characters.',
            'title.unique'   => 'This article title already exists, please choose another.',
            'description.required' => 'The article content is required.',
            'description.string'   => 'The article content must be a string.',
            'description.min'      => 'The article content must be at least 100 characters.',
            'description.max'      => 'The article content must not exceed 2000 characters.',
            'tags.array'    => 'The tags must be an array.',
            'tags.*.exists' => 'One or more of the selected tags do not exist.',
        ];
    }
}
