<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    /**
     * All categories with a count of their (open) jobs, for filter UIs.
     */
    public function allWithJobCounts(): Collection;

    /**
     * A single category with its open-job count (for the category page header).
     */
    public function findWithJobCount(int $id): Category;
}
