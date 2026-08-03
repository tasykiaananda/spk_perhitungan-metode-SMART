<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Contracts\KriteriaRepositoryInterface;
use App\Repositories\Contracts\AlternatifRepositoryInterface;
use App\Repositories\Contracts\PenilaianRepositoryInterface;
use App\Repositories\Contracts\HistoryRepositoryInterface;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;

use App\Repositories\Eloquent\KriteriaRepository;
use App\Repositories\Eloquent\AlternatifRepository;
use App\Repositories\Eloquent\PenilaianRepository;
use App\Repositories\Eloquent\HistoryRepository;
use App\Repositories\Eloquent\ActivityLogRepository;
use App\Repositories\Eloquent\SettingRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(KriteriaRepositoryInterface::class, KriteriaRepository::class);
        $this->app->bind(AlternatifRepositoryInterface::class, AlternatifRepository::class);
        $this->app->bind(PenilaianRepositoryInterface::class, PenilaianRepository::class);
        $this->app->bind(HistoryRepositoryInterface::class, HistoryRepository::class);
        $this->app->bind(ActivityLogRepositoryInterface::class, ActivityLogRepository::class);
        $this->app->bind(SettingRepositoryInterface::class, SettingRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
