<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $result = $this->authService->register($validated);

        return response()->json($result, 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $result = $this->authService->login($validated);
        if (!$result) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (($result['requires_verification'] ?? false) === true) {
            return response()->json($result, 403);
        }

        return response()->json($result);
    }

    public function verifyEmailOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $result = $this->authService->verifyEmailOtp($validated);

        return response()->json($result);
    }

    public function resendEmailOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $result = $this->authService->resendEmailOtp($validated['email']);

        return response()->json($result);
    }

    public function sendForgotPasswordOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $result = $this->authService->sendForgotPasswordOtp($validated['email']);

        return response()->json($result);
    }

    public function verifyForgotPasswordOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $result = $this->authService->verifyForgotPasswordOtp($validated);

        return response()->json($result);
    }

    public function resetPasswordWithOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'reset_token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $result = $this->authService->resetPasswordWithOtp($validated);

        return response()->json($result);
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
