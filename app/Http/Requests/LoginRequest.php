<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to make this request
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('auth.email_required'),
            'email.email' => __('auth.email_invalid'),
            'password.required' => __('auth.password_required'),
            'password.min' => __('auth.password_min'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'email' => trim($this->email),
        ]);
    }

    public function validateResolved()
    {
        parent::validateResolved();

        if (!User::where('email', $this->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => [__('auth.email_not_found')],
            ]);
        }
    }
}
