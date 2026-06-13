<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    public function definition(): array
    {
        $min = fake()->numberBetween(500, 3000);
        $max = $min + fake()->numberBetween(500, 5000);

        return [
            'category_id' => Category::factory(),
            'title' => fake()->sentence(4),
            'company_name' => fake()->company(),
            'company_about' => fake()->paragraph(),
            'short_description' => fake()->sentence(12),
            'description' => fake()->paragraphs(3, true),
            'required_skills' => fake()->randomElements(
                ['QuickBooks', 'Xero', 'Excel', 'IFRS', 'GAAP', 'Tax Filing', 'Payroll', 'Auditing'],
                fake()->numberBetween(2, 4)
            ),
            'budget_min' => $min,
            'budget_max' => $max,
            'delivery_days' => fake()->numberBetween(3, 45),
            'deadline' => fake()->dateTimeBetween('+1 week', '+2 months'),
            'attachments' => [],
            'status' => fake()->randomElement(['open', 'open', 'open', 'closed']),
        ];
    }

    public function closed(): static
    {
        return $this->state(['status' => 'closed']);
    }
}
