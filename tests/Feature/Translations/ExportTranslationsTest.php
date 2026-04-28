<?php

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create());
});

it('requires locale', function () {
    $response = $this->getJson('/api/translations/export');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['locale']);
});

it('exports translations by locale', function () {
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
        ->assertJsonMissingPath('tag');
});

it('exports translations by locale and tag', function () {
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
        ->assertJsonMissingPath('translations.auth.pin');
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
        ->assertJsonMissingPath('translations.auth.login');
});
