<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class JobClosedException extends Exception
{
    protected $message = 'This job is closed and no longer accepting bids.';

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 422); // Unprocessable
    }
}
