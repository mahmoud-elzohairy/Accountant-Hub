<?php

namespace App\Repositories\Eloquent;

use App\Models\Job;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class JobRepository implements JobRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 9): LengthAwarePaginator
    {
        return Job::query()
            ->with('category')
            ->withCount('bids')
            ->filter($filters)
            ->sortBy($filters['sort'] ?? null)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findWithDetails(int $id): Job
    {
        return Job::query()
            ->with('category')
            ->withCount('bids')
            ->findOrFail($id);
    }
}
