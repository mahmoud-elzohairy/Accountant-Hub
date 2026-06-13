<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function allWithJobCounts(): Collection
    {
        return Category::query()
            ->withCount(['jobs' => fn ($q) => $q->where('status', 'open')])
            ->orderBy('name')
            ->get();
    }
}
