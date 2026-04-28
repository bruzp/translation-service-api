<?php

namespace Database\Factories;

use App\Models\Locale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Locale>
 */
class LocaleFactory extends Factory
{
    protected $model = Locale::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->randomElement(['en', 'fr', 'es']) . '-' . fake()->unique()->numberBetween(1, 9999),
            'name' => fake()->languageCode(),
        ];
    }

    public function english(): self
    {
        return $this->state([
            'code' => 'en',
            'name' => 'English',
        ]);
    }
}
