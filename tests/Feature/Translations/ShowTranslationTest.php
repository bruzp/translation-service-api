<?php

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create());
});

it('shows a translation with locale and tags', function () {
    $locale = Locale::factory()->english()->create();
    $tag = Tag::factory()->web()->create();

    $translation = Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    $translation->tags()->attach($tag->id);

    $response = $this->getJson("/api/translations/{$translation->id}");

    $response->assertOk()
        ->assertJsonFragment([
            'id' => $translation->id,
            'key' => 'auth.login',
            'value' => 'Login',
        ])
        ->assertJsonFragment([
            'name' => 'web',
        ]);
});

it('returns 404 for missing translation', function () {
    $response = $this->getJson('/api/translations/999');

    $response->assertNotFound();
});
