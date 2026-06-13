<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Services\JobService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JobController extends Controller
{
    public function __construct(
        private readonly JobService $jobs,
    ) {}

    /**
     * GET /api/jobs — paginated, filterable, sortable listing.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['nullable', 'in:open,closed'],
            'budget_min' => ['nullable', 'numeric', 'min:0'],
            'budget_max' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', 'in:newest,budget_high,budget_low,deadline'],
        ]);

        return JobResource::collection($this->jobs->list($filters));
    }

    /**
     * GET /api/jobs/{job} — full job detail.
     */
    public function show(Request $request, int $job): JobResource
    {
        return new JobResource($this->jobs->find($job));
    }
}
