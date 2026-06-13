<?php

namespace Tests\Feature;

use App\Models\Bid;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BidTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'amount' => 1500,
            'delivery_days' => 14,
            'cover_letter' => 'I am confident I can complete this engagement accurately and on time.',
            'experience_summary' => 'Eight years of corporate tax experience.',
        ], $overrides);
    }

    public function test_a_guest_cannot_submit_a_bid(): void
    {
        $job = Job::factory()->create();

        $this->postJson("/api/jobs/{$job->id}/bids", $this->validPayload())
            ->assertUnauthorized();
    }

    public function test_an_authenticated_accountant_can_submit_a_bid(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $job = Job::factory()->create(['status' => 'open', 'budget_min' => 1000, 'budget_max' => 2000]);

        $this->postJson("/api/jobs/{$job->id}/bids", $this->validPayload())
            ->assertCreated()
            ->assertJsonPath('message', 'Your bid has been submitted successfully.');

        $this->assertDatabaseHas('bids', ['job_id' => $job->id, 'amount' => 1500]);
    }

    public function test_an_accountant_cannot_submit_two_bids_for_the_same_job(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $job = Job::factory()->create(['status' => 'open', 'budget_min' => 1000, 'budget_max' => 2000]);
        Bid::factory()->for($job)->for($user)->create();

        $this->postJson("/api/jobs/{$job->id}/bids", $this->validPayload())
            ->assertStatus(409)
            ->assertJsonPath('message', 'You have already submitted a bid for this job.');

        $this->assertSame(1, Bid::where('job_id', $job->id)->where('user_id', $user->id)->count());
    }

    public function test_a_bid_cannot_be_submitted_on_a_closed_job(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $job = Job::factory()->create(['status' => 'closed', 'budget_min' => 1000, 'budget_max' => 2000]);

        $this->postJson("/api/jobs/{$job->id}/bids", $this->validPayload())
            ->assertStatus(422)
            ->assertJsonPath('message', 'This job is closed and no longer accepting bids.');
    }

    public function test_bid_submission_is_validated(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $job = Job::factory()->create(['status' => 'open']);

        $this->postJson("/api/jobs/{$job->id}/bids", [
            'amount' => -5,
            'delivery_days' => 0,
            'cover_letter' => 'too short',
            'experience_summary' => '',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['amount', 'delivery_days', 'cover_letter', 'experience_summary']);
    }

    public function test_a_bid_below_the_client_budget_is_rejected(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $job = Job::factory()->create(['status' => 'open', 'budget_min' => 800, 'budget_max' => 1800]);

        $this->postJson("/api/jobs/{$job->id}/bids", $this->validPayload(['amount' => 200]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('amount');
    }

    public function test_a_bid_above_the_client_budget_is_rejected(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $job = Job::factory()->create(['status' => 'open', 'budget_min' => 800, 'budget_max' => 1800]);

        $this->postJson("/api/jobs/{$job->id}/bids", $this->validPayload(['amount' => 5000]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('amount');
    }

    public function test_a_bid_within_the_client_budget_is_accepted(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $job = Job::factory()->create(['status' => 'open', 'budget_min' => 800, 'budget_max' => 1800]);

        $this->postJson("/api/jobs/{$job->id}/bids", $this->validPayload(['amount' => 1200]))
            ->assertCreated();
    }

    public function test_an_accountant_sees_only_their_own_bids(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Bid::factory()->for($user)->count(2)->create();
        Bid::factory()->for($other)->create();

        Sanctum::actingAs($user);

        $this->getJson('/api/my-bids')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
