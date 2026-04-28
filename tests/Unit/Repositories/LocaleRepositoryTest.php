<?php

use App\Models\Locale;
use App\Repositories\LocaleRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('finds locale id by code', function () {
    $locale = Locale::factory()->english()->create();

    $repository = app(LocaleRepository::class);

    expect($repository->findIdByCode('en'))->toBe($locale->id);
});

it('throws exception when locale code does not exist', function () {
    $repository = app(LocaleRepository::class);

    $repository->findIdByCode('missing');
})->throws(ModelNotFoundException::class);
