<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'company_name' => $this->company_name,
            'short_description' => $this->short_description,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'budget' => [
                'min' => (float) $this->budget_min,
                'max' => (float) $this->budget_max,
            ],
            'delivery_days' => $this->delivery_days,
            'deadline' => $this->deadline->toDateString(),
            'status' => $this->status,
            'bids_count' => $this->whenCounted('bids'),
            'posted_at' => $this->created_at->toIso8601String(),

            // Detail-only fields (loaded by the show endpoint).
            'description' => $this->when($request->routeIs('jobs.show'), $this->description),
            'company_about' => $this->when($request->routeIs('jobs.show'), $this->company_about),
            'required_skills' => $this->when($request->routeIs('jobs.show'), $this->required_skills ?? []),
            'attachments' => $this->when($request->routeIs('jobs.show'), $this->attachments ?? []),

            // Whether the current authenticated accountant has already bid on this job.
            'has_bid' => $this->when(
                $request->user() !== null,
                fn () => $this->bids()->where('user_id', $request->user()?->id)->exists()
            ),
        ];
    }
}
