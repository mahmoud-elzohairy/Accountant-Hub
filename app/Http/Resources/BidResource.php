<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BidResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => (float) $this->amount,
            'delivery_days' => $this->delivery_days,
            'cover_letter' => $this->cover_letter,
            'experience_summary' => $this->experience_summary,
            'submitted_at' => $this->created_at->toIso8601String(),
            'job' => new JobResource($this->whenLoaded('job')),
        ];
    }
}
