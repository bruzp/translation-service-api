<?php

use App\Models\User;

it('logs in with valid credentials', function () {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => 'password',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'token',
        ]);

    expect($response->json('token'))->toBeString();
});

it('does not login with invalid password', function () {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => 'password',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertUnauthorized()
        ->assertJson([
            'type' => 'invalid_request_error',
            'message' => trans('auth.failed'),
        ]);
});

it('validates required login fields', function () {
    $response = $this->postJson('/api/auth/login', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors([
            'email',
            'password',
        ]);
});

it('logs out authenticated user', function () {
    $user = User::factory()->create();

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this
        ->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/auth/logout');

    $response->assertNoContent();

    expect($user->tokens()->count())->toBe(0);
});

it('does not logout unauthenticated user', function () {
    $response = $this->postJson('/api/auth/logout');

    $response->assertUnauthorized();
});

it('rate limits failed login attempts', function () {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => 'password',
    ]);

    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'wrong-password',
        ])->assertUnauthorized();
    }

    $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'wrong-password',
    ])->assertTooManyRequests();
});

it('clears login attempts after successful login', function () {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => 'password',
    ]);

    $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'wrong-password',
    ])->assertUnauthorized();

    $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'password',
    ])->assertOk();

    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'wrong-password',
        ])->assertUnauthorized();
    }

    $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'wrong-password',
    ])->assertTooManyRequests();
});
