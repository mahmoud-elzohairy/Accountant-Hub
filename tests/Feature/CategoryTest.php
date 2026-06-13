<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_categories_with_open_job_counts(): void
    {
        $category = Category::factory()->create();
        Job::factory()->for($category)->create(['status' => 'open']);
        Job::factory()->for($category)->create(['status' => 'closed']);

        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonPath('data.0.jobs_count', 1); // only open jobs counted
    }

    public function test_it_shows_a_single_category(): void
    {
        $category = Category::factory()->create(['name' => 'Bookkeeping']);

        $this->getJson("/api/categories/{$category->id}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Bookkeeping')
            ->assertJsonStructure(['data' => ['id', 'name', 'slug', 'jobs_count']]);
    }

    public function test_it_returns_404_for_a_missing_category(): void
    {
        $this->getJson('/api/categories/999')->assertNotFound();
    }
}
