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
            'name' => 'Sarah Mitchell',
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

        // Give the demo accountant a couple of bids so their dashboard isn't empty.
        Job::where('status', 'open')
            ->whereDoesntHave('bids', fn ($q) => $q->where('user_id', $demo->id))
            ->take(2)
            ->get()
            ->each(fn (Job $job) => Bid::factory()->for($job)->for($demo)->create());
    }
}
