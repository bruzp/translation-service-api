<?php

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

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
