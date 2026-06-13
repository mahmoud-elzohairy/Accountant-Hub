<?php

namespace App\Repositories\Contracts;

use App\Models\Job;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface JobRepositoryInterface
{
    /**
     * Paginated, filtered and sorted listing of jobs.
     *
     * @param  array{search?:string,category?:int,status?:string,budget_min?:float,budget_max?:float,sort?:string}  $filters
     */
    public function paginate(array $filters, int $perPage = 9): LengthAwarePaginator;

    /**
     * Find a single job with the relations needed for the detail page.
     */
    public function findWithDetails(int $id): Job;
}
