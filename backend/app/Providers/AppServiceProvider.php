<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(\Laravel\Sanctum\SanctumServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Rate limiter for general public API endpoints (60 requests/minute per IP)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiter for admission application submissions (10 submissions/hour per IP)
        RateLimiter::for('admissions', function (Request $request) {
            return Limit::perHour(20)->by($request->ip());
        });

        // Rate limiter for student portal exam lookups (30 requests/minute per IP)
        RateLimiter::for('student-portal', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });
    }
}
