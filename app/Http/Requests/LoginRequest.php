<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('auth.email_required'),
            'email.email' => __('auth.email_invalid'),
            'password.required' => __('auth.password_required'),
            'password.min' => __('auth.password_min'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => trim($this->email),
        ]);
    }

    public function validateResolved(): void
    {
        parent::validateResolved();

        if (!User::where('email', $this->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => [__('auth.email_not_found')],
            ]);
        }
    }
}
