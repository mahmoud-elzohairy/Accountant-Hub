<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBidRequest;
use App\Http\Resources\BidResource;
use App\Models\Job;
use App\Services\BidService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BidController extends Controller
{
    public function __construct(
        private readonly BidService $bids,
    ) {}

    /**
     * POST /api/jobs/{job}/bids — submit a bid (authenticated accountants only).
     */
    public function store(StoreBidRequest $request, Job $job): JsonResponse
    {
        $bid = $this->bids->submit($request->user(), $job, $request->validated());

        return response()->json([
            'message' => 'Your bid has been submitted successfully.',
            'data' => new BidResource($bid),
        ], 201);
    }

    /**
     * GET /api/my-bids — the authenticated accountant's submitted bids (dashboard).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return BidResource::collection($this->bids->listForUser($request->user()));
    }
}
