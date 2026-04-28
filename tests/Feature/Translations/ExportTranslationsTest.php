<?php

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('requires locale', function () {
    $response = $this->getJson('/api/translations/export');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['locale']);
});

it('exports translations by locale publicly', function () {
    $locale = Locale::factory()->english()->create();

    Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.logout',
        'value' => 'Logout',
    ]);

    $response = $this->getJson('/api/translations/export?locale=en');

    $response->assertOk()
        ->assertJsonPath('locale', 'en')
        ->assertJsonFragment([
            'auth.login' => 'Login',
            'auth.logout' => 'Logout',
        ])
        ->assertJsonMissingPath('tag')
        ->assertHeader('ETag');

    expect($response->headers->get('ETag'))->toStartWith('"')->toEndWith('"');

    $cacheControl = $response->headers->get('Cache-Control');

    expect($cacheControl)
        ->toContain('public')
        ->toContain('max-age=0')
        ->toContain('must-revalidate');
});

it('exports translations by locale and tag publicly', function () {
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

    $response = $this->getJson('/api/translations/export?locale=en&tag=web');

    $response->assertOk()
        ->assertJsonPath('locale', 'en')
        ->assertJsonPath('tag', 'web')
        ->assertJsonFragment([
            'auth.login' => 'Login',
        ])
        ->assertJsonMissing([
            'auth.pin' => 'PIN',
        ])
        ->assertHeader('ETag');

    $cacheControl = $response->headers->get('Cache-Control');

    expect($cacheControl)
        ->toContain('public')
        ->toContain('max-age=0')
        ->toContain('must-revalidate');
});

it('exports latest updated translation value', function () {
    $locale = Locale::factory()->english()->create();

    $translation = Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    $translation->update([
        'value' => 'Sign in',
    ]);

    $response = $this->getJson('/api/translations/export?locale=en');

    $response->assertOk()
        ->assertJsonFragment([
            'auth.login' => 'Sign in',
        ]);
});

it('does not export deleted translations', function () {
    $locale = Locale::factory()->english()->create();

    $translation = Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    $translation->delete();

    $response = $this->getJson('/api/translations/export?locale=en');

    $response->assertOk()
        ->assertJsonMissing([
            'auth.login' => 'Login',
        ]);
});

it('returns not modified when etag matches', function () {
    $locale = Locale::factory()->english()->create();

    Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    $firstResponse = $this->getJson('/api/translations/export?locale=en');

    $etag = $firstResponse->headers->get('ETag');

    $secondResponse = $this
        ->withHeader('If-None-Match', $etag)
        ->getJson('/api/translations/export?locale=en');

    $secondResponse->assertStatus(304)
        ->assertHeader('ETag', $etag);

    $cacheControl = $secondResponse->headers->get('Cache-Control');

    expect($cacheControl)
        ->toContain('public')
        ->toContain('max-age=0')
        ->toContain('must-revalidate');
});

it('returns new response when etag does not match after translation update', function () {
    $locale = Locale::factory()->english()->create();

    $translation = Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    $firstResponse = $this->getJson('/api/translations/export?locale=en');

    $oldEtag = $firstResponse->headers->get('ETag');

    $translation->update([
        'value' => 'Sign in',
    ]);

    $secondResponse = $this
        ->withHeader('If-None-Match', $oldEtag)
        ->getJson('/api/translations/export?locale=en');

    $secondResponse->assertOk()
        ->assertJsonFragment([
            'auth.login' => 'Sign in',
        ]);

    expect($secondResponse->headers->get('ETag'))->not->toBe($oldEtag);
});
