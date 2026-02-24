<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Invoice;
use App\Models\SalesReturn;
use App\Observers\InvoiceObserver;
use App\Observers\SalesReturnObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;

final class AppServiceProvider extends ServiceProvider
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
        Model::unguard();
        Model::shouldBeStrict();
        $this->configureRateLimiting();
        Invoice::observe(InvoiceObserver::class);
        SalesReturn::observe(SalesReturnObserver::class);
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('auth', fn (Request $request): Limit => Limit::perMinute(5)
            ->by($request->ip())
            ->response(function (): never {
                throw ValidationException::withMessages([
                    'email' => ['Too many attempts. Please try again later.'],
                ]);
            }));
    }
}
