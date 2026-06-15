<?php

namespace App\Providers;

use App\Repositories\BahanMasukRepository;
use App\Repositories\BahanRepository;
use App\Repositories\Contracts\BahanMasukRepositoryInterface;
use App\Repositories\Contracts\BahanRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        // $this->app->bind(BahanRepositoryInterface::class, BahanRepository::class);
    }
}