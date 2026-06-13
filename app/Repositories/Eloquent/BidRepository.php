<?php

namespace App\Repositories\Eloquent;

use App\Models\Bid;
use App\Repositories\Contracts\BidRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BidRepository implements BidRepositoryInterface
{
    public function create(array $attributes): Bid
    {
        return Bid::create($attributes);
    }

    public function existsForUserAndJob(int $userId, int $jobId): bool
    {
        return Bid::where('user_id', $userId)
            ->where('job_id', $jobId)
            ->exists();
    }

    public function paginateForUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Bid::query()
            ->where('user_id', $userId)
            ->with(['job.category'])
            ->latest()
            ->paginate($perPage);
    }
}
