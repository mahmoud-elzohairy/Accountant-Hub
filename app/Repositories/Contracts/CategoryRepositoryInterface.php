<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    /**
     * All categories with a count of their (open) jobs, for filter UIs.
     */
    public function allWithJobCounts(): Collection;
}
