<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categories,
    ) {}

    /**
     * GET /api/categories — all categories with open-job counts (for filters).
     */
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection($this->categories->list());
    }
}
