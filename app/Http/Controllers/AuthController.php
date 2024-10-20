<?php

namespace App\Http\Controllers;


use App\Helpers\ResponseHandler;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    //TODO if need two step login or register it will changed
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return ResponseHandler::error(__('auth.invalid_credentials'), 401);
            }
        } catch (JWTException $e) {
            return ResponseHandler::error(__('auth.token_error'), 500);
        }

        return ResponseHandler::success(compact('token'));
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return ResponseHandler::success(['token' => $token, 'user' => $user], __('auth.registration_successful'), 201);
    }
}


