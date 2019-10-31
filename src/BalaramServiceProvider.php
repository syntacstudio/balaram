<?php

namespace Syntac\Balaram;

use Illuminate\Support\ServiceProvider;

class BalaramServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/balaram.php' => config_path('balaram.php'),
        ], 'balaram.config');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->commands([
                'Syntac\Balaram\Commands\BackupRun',
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/balaram.php', 'balaram');
    }
}
