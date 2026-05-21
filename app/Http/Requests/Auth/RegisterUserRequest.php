<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'min:3'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'name is required',
            'name.max' => 'name is too long',
            'name.min' => 'name is too short',
            'name.string' => 'name should be string',
            'email.email' => 'email is invalid',
            'email.max' => 'email is too long',
            'email.required' => 'email is required',
            'email.unique' => 'invalid email address try again',
            'password.required' => 'password is required',
            'password.string' => 'password should be string',
            'password.confirmed' => 'password confirmation does not match',
            'password.min' => 'password is too short',
        ];
    }
}
