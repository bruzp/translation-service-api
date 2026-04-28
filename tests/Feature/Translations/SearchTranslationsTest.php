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

it('searches translations by key', function () {
    $locale = Locale::factory()->english()->create();

    Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    Translation::factory()->forLocale($locale)->create([
        'key' => 'dashboard.title',
        'value' => 'Dashboard',
    ]);

    $response = $this->getJson('/api/translations?key=auth');

    $response->assertOk()
        ->assertJsonFragment(['key' => 'auth.login'])
        ->assertJsonMissing(['key' => 'dashboard.title']);
});

it('searches translations by value', function () {
    $locale = Locale::factory()->english()->create();

    Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    Translation::factory()->forLocale($locale)->create([
        'key' => 'dashboard.title',
        'value' => 'Dashboard',
    ]);

    $response = $this->getJson('/api/translations?value=Dashboard');

    $response->assertOk()
        ->assertJsonFragment(['value' => 'Dashboard'])
        ->assertJsonMissing(['value' => 'Login']);
});

it('searches translations by tag', function () {
    $locale = Locale::factory()->english()->create();
    $tag = Tag::factory()->web()->create();

    $matched = Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    $unmatched = Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.logout',
        'value' => 'Logout',
    ]);

    $matched->tags()->attach($tag->id);

    $response = $this->getJson("/api/translations?tags[]={$tag->id}");

    $response->assertOk()
        ->assertJsonFragment(['key' => $matched->key])
        ->assertJsonMissing(['key' => $unmatched->key]);
});

it('paginates translations', function () {
    $locale = Locale::factory()->english()->create();

    Translation::factory()
        ->count(15)
        ->forLocale($locale)
        ->create();

    $response = $this->getJson('/api/translations?page=1&perPage=10');

    $response->assertOk()
        ->assertJsonPath('meta.per_page', 10)
        ->assertJsonPath('meta.current_page', 1);
});
