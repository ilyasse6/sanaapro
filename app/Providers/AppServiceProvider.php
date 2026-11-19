<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Barryvdh\DomPDF\Facade\Pdf;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('pdf', function() {
            return new Pdf();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Uniquement si l'URL publique est vraiment en HTTPS (évite les redirections
        // cassées / lentes quand on sert encore en HTTP via IP).
        $appUrl = (string) config('app.url');
        if (str_starts_with($appUrl, 'https://') || filter_var(env('FORCE_HTTPS', false), FILTER_VALIDATE_BOOLEAN)) {
            URL::forceScheme('https');
        }
    }
}
