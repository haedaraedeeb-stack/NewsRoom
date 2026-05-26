<?php

namespace App\Http\Requests\UserProfile;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserProfileRequest extends FormRequest
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
            'birth_date'  => ['required', 'date', 'before:today'],
            'gender'      => ['required', 'in:male,female'],
            'nationality' => ['required', 'string', 'max:100'],
            'job_title'   => ['required', 'string', 'max:100'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'birth_date.required'  => 'Birth date is required.',
            'birth_date.date'      => 'Birth date must be a valid date.',
            'birth_date.before'    => 'Birth date must be before today.',
            'gender.required'      => 'Gender is required.',
            'gender.in'            => 'Gender must be male or female.',
            'nationality.required' => 'Nationality is required.',
            'nationality.max'      => 'Nationality must not exceed 100 characters.',
            'job_title.required'   => 'Job title is required.',
            'job_title.max'        => 'Job title must not exceed 100 characters.',
            'attachment.mimes' => 'The attachment must be a file of type: jpg, jpeg, png.',
            'attachment.file'  => 'The attachment must be a file of type: jpg, jpeg, png.',
            'attachment.max'  => 'The attachment must not exceed 2048.',
        ];
    }
}
