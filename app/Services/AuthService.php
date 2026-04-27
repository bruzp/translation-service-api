<?php

namespace App\Services;

use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    private const int MAX_LOGIN_ATTEMPTS = 5;

    public function getAuthenticatedUser(): User
    {
        $user = auth()->user();

        if ($user instanceof User) {
            return $user;
        }

        abort(Response::HTTP_UNAUTHORIZED);
    }

    public function authenticate(AuthRequest $request): ?User
    {
        $this->ensureIsNotRateLimited($request);

        $credentials = [
            'email' => $request->safe()->input('email'),
            'password' => $request->safe()->input('password'),
        ];

        if (! Auth::attempt($credentials)) {
            RateLimiter::hit($this->throttleKey($request));

            throw new HttpResponseException(
                response()->json([
                    'type' => 'invalid_request_error',
                    'message' => trans('auth.failed'),
                ], Response::HTTP_UNAUTHORIZED),
            );
        }

        RateLimiter::clear($this->throttleKey($request));

        return Auth::user();
    }

    private function ensureIsNotRateLimited(AuthRequest $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), self::MAX_LOGIN_ATTEMPTS)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));
        throw new HttpResponseException(
            response()->json([
                'type' => 'invalid_request_error',
                'message' => trans('auth.throttle', ['seconds' => $seconds]),
            ], Response::HTTP_TOO_MANY_REQUESTS),
        );
    }

    private function throttleKey(AuthRequest $request): string
    {
        return Str::transliterate(Str::lower($request->safe()->input('loginId')).'|'.request()->ip());
    }
}
