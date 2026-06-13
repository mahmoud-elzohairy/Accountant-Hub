<?php

namespace App\Services;

use App\Exceptions\DuplicateBidException;
use App\Exceptions\JobClosedException;
use App\Models\Bid;
use App\Models\Job;
use App\Models\User;
use App\Repositories\Contracts\BidRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\UniqueConstraintViolationException;

class BidService
{
    public function __construct(
        private readonly BidRepositoryInterface $bids,
    ) {}

    /**
     * Submit a bid on behalf of an accountant.
     *
     * Business rules:
     *  - The job must be open.
     *  - An accountant may submit only one bid per job.
     *
     * @throws JobClosedException
     * @throws DuplicateBidException
     */
    public function submit(User $accountant, Job $job, array $data): Bid
    {
        if (! $job->isOpen()) {
            throw new JobClosedException;
        }

        if ($this->bids->existsForUserAndJob($accountant->id, $job->id)) {
            throw new DuplicateBidException;
        }

        try {
            return $this->bids->create([
                ...$data,
                'job_id' => $job->id,
                'user_id' => $accountant->id,
            ]);
        } catch (UniqueConstraintViolationException) {
            // Two concurrent requests slipped past the check above; the
            // unique (job_id, user_id) index is the final guard.
            throw new DuplicateBidException;
        }
    }

    public function listForUser(User $accountant): LengthAwarePaginator
    {
        return $this->bids->paginateForUser($accountant->id);
    }
}
