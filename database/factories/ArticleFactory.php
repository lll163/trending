<?php

namespace Database\Factories;

// AI-GEN-BEGIN
use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'magazine_issue_id' => null,
            'slug' => 'article-'.fake()->unique()->numerify('########'),
            'title' => fake()->sentence(4),
            'body' => '# '.fake()->words(3, true)."\n\n".fake()->paragraph(),
            'sort_order' => 0,
            'status' => 'draft',
            'published_at' => null,
        ];
    }

    /**
     * 已发布（前台可读）。
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
// AI-GEN-END
