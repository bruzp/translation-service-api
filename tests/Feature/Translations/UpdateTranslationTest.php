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

it('updates a translation and syncs tags', function () {
    $locale = Locale::factory()->english()->create();
    $tag = Tag::factory()->mobile()->create();

    $translation = Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    $response = $this->putJson("/api/translations/{$translation->id}", [
        'localeId' => $locale->id,
        'key' => 'auth.sign_in',
        'value' => 'Sign in',
        'tags' => [$tag->id],
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'key' => 'auth.sign_in',
            'value' => 'Sign in',
        ]);

    $this->assertDatabaseHas('translations', [
        'id' => $translation->id,
        'key' => 'auth.sign_in',
        'value' => 'Sign in',
    ]);

    $this->assertDatabaseHas('translation_tags', [
        'translation_id' => $translation->id,
        'tag_id' => $tag->id,
    ]);
});

it('prevents duplicate key for same locale during update', function () {
    $locale = Locale::factory()->english()->create();

    Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
    ]);

    $translation = Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.logout',
    ]);

    $response = $this->putJson("/api/translations/{$translation->id}", [
        'localeId' => $locale->id,
        'key' => 'auth.login',
        'value' => 'Duplicate',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['key']);
});

it('returns 404 when updating missing translation', function () {
    $locale = Locale::factory()->english()->create();

    $response = $this->putJson('/api/translations/999', [
        'localeId' => $locale->id,
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    $response->assertNotFound();
});
