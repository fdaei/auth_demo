<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHandler;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
   // TODO: Consider the scenario where the token is valid but user access is revoked in the future.
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
        try {
            $user = $this->authService->register($request->validated());
            $token = $this->authService->getToken($user);

            return ResponseHandler::success(['token' => $token, 'user' => $user], __('auth.registration_successful'), 201);
        } catch (\Exception $e) {
            return ResponseHandler::error(__('auth.registration_failed'), 500);
        }
    }

    public function getUser(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $this->authService->getAuthenticatedUser();
            return ResponseHandler::success(['user' => $user]);
        } catch (\Exception $e) {
            return ResponseHandler::error($e->getMessage(), 400);
        }
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        $this->authService->logout();
        return ResponseHandler::success([], __('auth.logged_out'), 200);
    }
}
