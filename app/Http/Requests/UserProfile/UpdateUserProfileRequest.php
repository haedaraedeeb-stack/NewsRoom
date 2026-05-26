<?php

namespace App\Http\Requests\UserProfile;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
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
            'nationality' => $this->nationality ? trim(ucfirst(strtolower($this->nationality))) : null,
            'job_title'   => $this->job_title ? trim(ucfirst(strtolower($this->job_title))) : null,
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
            'birth_date'  => ['sometimes', 'date', 'before:today'],
            'gender'      => ['sometimes', 'in:male,female'],
            'nationality' => ['sometimes', 'string', 'max:100'],
            'job_title'   => ['sometimes', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'birth_date.date'   => 'Birth date must be a valid date.',
            'birth_date.before' => 'Birth date must be before today.',
            'gender.in'         => 'Gender must be male or female.',
            'nationality.max'   => 'Nationality must not exceed 100 characters.',
            'job_title.max'     => 'Job title must not exceed 100 characters.',
        ];
    }
}
