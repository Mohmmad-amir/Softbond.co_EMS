<?php

namespace App\Providers;

use App\Models\SalaryRequest;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        //
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $view->with('pendingSalary', SalaryRequest::where('status', 'pending')->count());
            }
        });
    }
}
