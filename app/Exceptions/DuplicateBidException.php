<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class DuplicateBidException extends Exception
{
    protected $message = 'You have already submitted a bid for this job.';

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 409); // Conflict
    }
}
