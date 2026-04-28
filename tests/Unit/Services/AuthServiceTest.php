<?php

use App\Models\User;
use App\Services\AuthService;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('gets the authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $service = app(AuthService::class);

    expect($service->getAuthenticatedUser())->toBe($user);
});

it('aborts when getting authenticated user without login', function () {
    $service = app(AuthService::class);

    $service->getAuthenticatedUser();
})->throws(HttpException::class);
