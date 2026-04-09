<?php

namespace Database\Factories;

// AI-GEN-BEGIN
use App\Models\MagazineIssue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MagazineIssue>
 */
class MagazineIssueFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'issue_code' => 'issue-'.fake()->unique()->numerify('####'),
            'title' => fake()->sentence(3),
            'cover_path' => null,
            'catalog_json' => null,
            'status' => 'draft',
            'published_at' => null,
        ];
    }

    /**
     * 已发布状态（前台可见）。
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
