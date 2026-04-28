<?php

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create());
});

it('deletes a translation and detaches tags', function () {
    $locale = Locale::factory()->english()->create();
    $tag = Tag::factory()->web()->create();

    $translation = Translation::factory()->forLocale($locale)->create([
        'key' => 'auth.login',
        'value' => 'Login',
    ]);

    $translation->tags()->attach($tag->id);

    $response = $this->deleteJson("/api/translations/{$translation->id}");

    $response->assertNoContent();

    $this->assertSoftDeleted('translations', [
        'id' => $translation->id,
    ]);

    $this->assertDatabaseMissing('translation_tags', [
        'translation_id' => $translation->id,
        'tag_id' => $tag->id,
    ]);
});

it('returns 404 when deleting missing translation', function () {
    $response = $this->deleteJson('/api/translations/999');

    $response->assertNotFound();
});
