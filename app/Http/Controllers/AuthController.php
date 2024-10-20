<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHandler;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $token = $this->authService->login($credentials);

        if (!$token) {
            return ResponseHandler::error(__('auth.invalid_credentials'), 401);
        }

        return ResponseHandler::success(['token' => $token]);
    }

    public function register(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $this->authService->register($request->validated());
        $token = $this->authService->getToken($user);

        return ResponseHandler::success(['token' => $token, 'user' => $user], __('auth.registration_successful'), 201);
    }
}
