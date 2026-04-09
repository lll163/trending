<?php

namespace Database\Factories;

// AI-GEN-BEGIN
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $slug = 'tag-'.fake()->unique()->numberBetween(1000, 9999999);

        return [
            'slug' => $slug,
            'title' => fake()->words(2, true),
        ];
    }
}
// AI-GEN-END
