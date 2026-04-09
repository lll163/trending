<?php

namespace Database\Factories;

// AI-GEN-BEGIN
use App\Models\Submission;
use App\Models\SubmissionStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'github_owner' => 'demoowner',
            'github_repo' => 'repo-'.fake()->unique()->numberBetween(1000, 9999999),
            'pitch' => fake()->sentence(),
            'status' => SubmissionStatus::PENDING_REVIEW,
            'reject_reason' => null,
            'repos_snapshot_id' => null,
        ];
    }
}
// AI-GEN-END
