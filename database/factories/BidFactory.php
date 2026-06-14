<?php

namespace Database\Factories;

use App\Models\Bid;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bid>
 */
class BidFactory extends Factory
{
    public function definition(): array
    {
        return [
            'job_id' => Job::factory(),
            'user_id' => User::factory(),
            'amount' => fake()->numberBetween(800, 7000),
            'delivery_days' => fake()->numberBetween(3, 40),
            'cover_letter' => fake()->paragraphs(2, true),
            'experience_summary' => fake()->sentence(20),
        ];
    }

    /**
     * Long cover letter and experience summary, useful for demonstrating the
     * "Show more / Show less" expand toggle on the dashboard.
     */
    public function long(): static
    {
        return $this->state(fn () => [
            'cover_letter' => fake()->paragraphs(5, true),
            'experience_summary' => fake()->paragraphs(3, true),
        ]);
    }
}
