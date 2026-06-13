<?php

namespace App\Providers;

use App\Repositories\Contracts\BidRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\JobRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\BidRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\JobRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Map repository contracts to their Eloquent implementations.
     * Swapping the data layer (e.g. for tests) is a one-line change here.
     */
    public array $bindings = [
        JobRepositoryInterface::class => JobRepository::class,
        BidRepositoryInterface::class => BidRepository::class,
        CategoryRepositoryInterface::class => CategoryRepository::class,
        UserRepositoryInterface::class => UserRepository::class,
    ];
}
