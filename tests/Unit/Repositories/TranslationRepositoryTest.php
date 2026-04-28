<?php

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Repositories\TranslationRepository;

it('exports translations by locale id as key value collection', function () {
    $locale = Locale::factory()->english()->create();

    Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.logout',
        'value' => 'Logout',
    ]);

    $repository = app(TranslationRepository::class);

    $result = $repository->exportByLocaleAndTag($locale->id, null);

    expect($result->toArray())->toBe([
        'auth.login' => 'Login',
        'auth.logout' => 'Logout',
    ]);
});

it('exports translations filtered by tag', function () {
    $locale = Locale::factory()->english()->create();
    $web = Tag::factory()->web()->create();
    $mobile = Tag::factory()->mobile()->create();

    $webTranslation = Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    $mobileTranslation = Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.pin',
        'value' => 'PIN',
    ]);

    $webTranslation->tags()->attach($web->id);
    $mobileTranslation->tags()->attach($mobile->id);

    $repository = app(TranslationRepository::class);

    $result = $repository->exportByLocaleAndTag($locale->id, 'web');

    expect($result->toArray())->toBe([
        'auth.login' => 'Login',
    ]);
});
