<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'company_name',
        'company_about',
        'short_description',
        'description',
        'required_skills',
        'budget_min',
        'budget_max',
        'delivery_days',
        'deadline',
        'attachments',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'required_skills' => 'array',
            'attachments' => 'array',
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
            'deadline' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Apply listing filters from the request query parameters.
     *
     * Supported: search (title), category (id), budget_min, budget_max, status.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, fn (Builder $q, string $search) => $q->where('title', 'like', "%{$search}%"))
            ->when($filters['category'] ?? null, fn (Builder $q, $category) => $q->where('category_id', $category))
            ->when($filters['status'] ?? null, fn (Builder $q, string $status) => $q->where('status', $status))
            ->when($filters['budget_min'] ?? null, fn (Builder $q, $min) => $q->where('budget_max', '>=', $min))
            ->when($filters['budget_max'] ?? null, fn (Builder $q, $max) => $q->where('budget_min', '<=', $max));
    }

    /**
     * Apply sorting. Supported: newest (default), budget_high, budget_low, deadline.
     */
    public function scopeSortBy(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'budget_high' => $query->orderByDesc('budget_max'),
            'budget_low' => $query->orderBy('budget_min'),
            'deadline' => $query->orderBy('deadline'),
            default => $query->latest(),
        };
    }
}
