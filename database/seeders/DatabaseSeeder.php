<?php

namespace Database\Seeders;

use App\Models\Bid;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            JobSeeder::class,
        ]);

        // Primary demo accountant (credentials shared in the README).
        $demo = User::create([
            'name' => 'Mahmoud Elzohairy',
            'email' => 'accountant@demo.test',
            'headline' => 'Certified Public Accountant · 8 yrs',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // A pool of other accountants so jobs show realistic bid counts.
        $others = User::factory(8)->create();

        // Spread bids across open jobs, one bid per (user, job).
        Job::where('status', 'open')->get()->each(function (Job $job) use ($others) {
            $others->random(rand(1, 4))->each(function (User $user) use ($job) {
                Bid::factory()->for($job)->for($user)->create();
            });
        });

        // Give the demo accountant a bid on every open job so the dashboard is
        // well populated and its pagination is demonstrable (more than one page).
        // Each bid amount sits within that job's budget range, matching the API rule.
        Job::where('status', 'open')->get()->each(function (Job $job) use ($demo) {
            Bid::factory()->long()->for($job)->for($demo)->create([
                'amount' => rand((int) $job->budget_min, (int) $job->budget_max),
            ]);
        });
    }
}
