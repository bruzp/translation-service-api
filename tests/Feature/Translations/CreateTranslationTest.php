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

it('creates a translation with tags', function () {
    $locale = Locale::factory()->english()->create();
    $tag = Tag::factory()->web()->create();

    $response = $this->postJson('/api/translations', [
        'localeId' => $locale->id,
        'key' => 'auth.login',
        'value' => 'Login',
        'tags' => [$tag->id],
    ]);

    $response->assertCreated()
        ->assertJsonFragment([
            'key' => 'auth.login',
            'value' => 'Login',
        ]);

    $translation = Translation::where('key', 'auth.login')->first();

    expect($translation)->not->toBeNull();

    $this->assertDatabaseHas('translation_tags', [
        'translation_id' => $translation->id,
        'tag_id' => $tag->id,
    ]);
});

it('requires a valid locale', function () {
    $response = $this->postJson('/api/translations', [
        'localeId' => 999,
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['localeId']);
});

it('prevents duplicate key for same locale', function () {
    $locale = Locale::factory()->english()->create();

    Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    $response = $this->postJson('/api/translations', [
        'localeId' => $locale->id,
        'key' => 'auth.login',
        'value' => 'Duplicate login',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['key']);
});
