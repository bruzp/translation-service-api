<?php

namespace Database\Factories;

use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Translation>
 */
class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition(): array
    {
        return [
            'locale_id' => Locale::factory(),
            'key' => 'test.key.' . fake()->unique()->numberBetween(1, 999999),
            'value' => fake()->sentence(),
        ];
    }

    public function forLocale(Locale $locale): self
    {
        return $this->state([
            'locale_id' => $locale->id,
        ]);
    }
}
