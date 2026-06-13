<?php

namespace App\Repositories\Contracts;

use App\Models\Bid;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BidRepositoryInterface
{
    public function create(array $attributes): Bid;

    public function existsForUserAndJob(int $userId, int $jobId): bool;

    /**
     * Paginated bids submitted by a given accountant (with their jobs).
     */
    public function paginateForUser(int $userId, int $perPage = 10): LengthAwarePaginator;
}
