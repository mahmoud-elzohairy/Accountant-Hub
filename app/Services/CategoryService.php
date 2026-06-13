<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categories,
    ) {}

    public function list(): Collection
    {
        return $this->categories->allWithJobCounts();
    }

    public function find(int $id): Category
    {
        return $this->categories->findWithJobCount($id);
    }
}
