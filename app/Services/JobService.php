<?php

namespace App\Services;

use App\Models\Job;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class JobService
{
    public function __construct(
        private readonly JobRepositoryInterface $jobs,
    ) {}

    public function list(array $filters): LengthAwarePaginator
    {
        return $this->jobs->paginate($filters);
    }

    public function find(int $id): Job
    {
        return $this->jobs->findWithDetails($id);
    }
}
