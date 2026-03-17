<?php

namespace App\Providers;

use App\Models\Event;
use App\Policies\EventPolicy;
use App\Support\AsaasClient;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AsaasClient::class, fn () => new AsaasClient(
            config('asaas.api_url'),
            config('asaas.api_key'),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Event::class, EventPolicy::class);
    }
}
