<?php

namespace App\Http\Requests\Comment;

use APP\Enums\ArticleStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'body' => trim($this->body),
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
            'body' => ['required', 'string', 'min:3', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'The comment body is required.',
            'body.string'   => 'The comment body must be a string.',
            'body.min'      => 'The comment body must be at least 3 characters.',
            'body.max'      => 'The comment body must not exceed 1000 characters.',
        ];
    }
}
