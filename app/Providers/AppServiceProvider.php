<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\Rebilly\Client::class, function ($app) {
            return new \Rebilly\Client([
                'apiKey' => env('REBILLY_API_SECRET'),
                'baseUrl' => \Rebilly\Client::SANDBOX_HOST,
            ]);
        });
    }
}
