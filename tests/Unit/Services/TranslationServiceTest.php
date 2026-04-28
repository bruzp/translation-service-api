<?php

use App\Models\Locale;
use App\Models\Translation;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('formats export response without tag', function () {
    $locale = Locale::factory()->english()->create();

    Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    $service = app(TranslationService::class);

    $result = $service->exportTranslations('en', null);

    expect($result)
        ->toHaveKey('locale', 'en')
        ->not->toHaveKey('tag')
        ->and($result['translations']->toArray())
        ->toBe([
            'auth.login' => 'Login',
        ]);
});

it('formats export response with tag', function () {
    $locale = Locale::factory()->english()->create();
    $tag = \App\Models\Tag::factory()->web()->create();

    $translation = Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    $translation->tags()->attach($tag->id);

    $service = app(TranslationService::class);

    $result = $service->exportTranslations('en', 'web');

    expect($result)
        ->toHaveKey('locale', 'en')
        ->toHaveKey('tag', 'web')
        ->and($result['translations']->toArray())
        ->toBe([
            'auth.login' => 'Login',
        ]);
});
