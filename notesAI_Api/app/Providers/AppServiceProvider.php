<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('notes-api', function (Request $request) {

            return Limit::perMinute(60)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {

                    return response()->json([
                        'success' => false,
                        'message' => 'Rate limit exceeded. Please wait before making more requests.',
                        'retry_after' => 60
                    ], 429, $headers);

                });

        });
    }
}