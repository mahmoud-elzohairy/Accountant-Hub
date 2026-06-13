<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_jobs_with_pagination_and_bid_counts(): void
    {
        Job::factory()->count(3)->create();

        $this->getJson('/api/jobs')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'title', 'company_name', 'budget', 'bids_count', 'status']],
                'meta' => ['current_page', 'last_page', 'total'],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_it_filters_jobs_by_category(): void
    {
        $taxes = Category::factory()->create();
        $audit = Category::factory()->create();
        Job::factory()->for($taxes)->create();
        Job::factory()->for($audit)->count(2)->create();

        $this->getJson("/api/jobs?category={$audit->id}")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_it_searches_jobs_by_title(): void
    {
        Job::factory()->create(['title' => 'Quarterly VAT Return Review']);
        Job::factory()->create(['title' => 'Payroll Setup']);

        $this->getJson('/api/jobs?search=VAT')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Quarterly VAT Return Review');
    }

    public function test_it_sorts_jobs_by_highest_budget(): void
    {
        Job::factory()->create(['title' => 'Low', 'budget_min' => 100, 'budget_max' => 500]);
        Job::factory()->create(['title' => 'High', 'budget_min' => 5000, 'budget_max' => 9000]);

        $this->getJson('/api/jobs?sort=budget_high')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'High');
    }

    public function test_it_shows_a_single_job_with_full_details(): void
    {
        $job = Job::factory()->create(['description' => 'Full detailed description here.']);

        $this->getJson("/api/jobs/{$job->id}")
            ->assertOk()
            ->assertJsonPath('data.description', 'Full detailed description here.')
            ->assertJsonStructure(['data' => ['required_skills', 'company_about', 'attachments']]);
    }

    public function test_it_returns_404_for_a_missing_job(): void
    {
        $this->getJson('/api/jobs/999')->assertNotFound();
    }
}
