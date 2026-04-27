<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function login(AuthRequest $request): JsonResponse
    {
        $user = $this->authService->authenticate($request);
        $token = $user->createToken('app-token')->plainTextToken;

        return response()->json([
            'token' => $token,
        ]);
    }

    public function logout(): void
    {
        $this->authService->getAuthenticatedUser()->currentAccessToken()->delete();
    }
}
