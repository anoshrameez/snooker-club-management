<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Support Serverless read-only environments like Vercel
        if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL')) {
            $storage = '/tmp/storage';
            $dirs = [
                $storage,
                $storage . '/framework/views',
                $storage . '/framework/cache/data',
                $storage . '/framework/sessions',
                $storage . '/logs',
                '/tmp/views',
                '/tmp/bootstrap/cache',
            ];
            foreach ($dirs as $dir) {
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
            }
            $this->app->useStoragePath($storage);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
            str_contains(request()->url(), 'trycloudflare.com') ||
            str_contains(request()->url(), 'https://')
        ) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
