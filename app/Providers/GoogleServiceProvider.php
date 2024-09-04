<?php

namespace App\Providers;

use Google\Client;
use Google\Service\YouTube;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class GoogleServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Youtube::class, function () {
            $client = new Client;
            $client->setApplicationName(config('app.name'));
            $client->setAccessToken(config('services.google.api_key'));

            return new YouTube($client);
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [Youtube::class];
    }
}
