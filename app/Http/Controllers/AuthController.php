<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    #[OA\Post(
        path: '/auth/login',
        tags: ['Auth'],
        summary: 'Login user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest')
        ),
        responses: [
        new OA\Response(
            response: 200,
            description: 'Login successful',
            content: new OA\JsonContent(ref: '#/components/schemas/LoginResponse')
        ),
        new OA\Response(response: 401, description: 'Invalid credentials'),
        new OA\Response(response: 422, description: 'Validation error'),
    ]
    )]
    public function login(AuthRequest $request): JsonResponse
    {
        $user = $this->authService->authenticate($request);
        $token = $user->createToken('app-token')->plainTextToken;

        return response()->json([
            'token' => $token,
        ]);
    }

    #[OA\Post(
        path: '/auth/logout',
        tags: ['Auth'],
        summary: 'Logout user',
        security: [['sanctum' => []]],
        responses: [
         new OA\Response(response: 204, description: 'Logged out'),
         new OA\Response(response: 401, description: 'Unauthenticated'),
    ]
    )]
    public function logout(): Response
    {
        $this->authService->getAuthenticatedUser()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
