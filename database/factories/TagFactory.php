<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['web', 'mobile', 'desktop']) . '-' . fake()->unique()->numberBetween(1, 9999),
        ];
    }

    public function web(): self
    {
        return $this->state([
            'name' => 'web',
        ]);
    }

    public function mobile(): self
    {
        return $this->state([
            'name' => 'mobile',
        ]);
    }

    public function desktop(): self
    {
        return $this->state([
            'name' => 'desktop',
        ]);
    }
}
