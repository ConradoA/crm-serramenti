<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        \App\Models\Estimate::observe(\App\Observers\EstimateObserver::class);
        \App\Models\EstimateItem::observe(\App\Observers\EstimateItemObserver::class);

        if (app()->environment('production')) {
            // Forzar HTTPS
            \Illuminate\Support\Facades\URL::forceScheme('https');

            // Asegurar que Livewire usa la ruta correcta
            \Livewire\Livewire::setUpdateRoute(function ($handle) {
                return \Illuminate\Support\Facades\Route::post('/livewire/update', $handle)
                    ->middleware(['web']);
            });
        }
    }
}
