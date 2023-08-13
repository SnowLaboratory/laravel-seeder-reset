<?php

namespace SnowBuilds\SeederReset;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use SnowBuilds\SeederReset\Console\SyncMatrix;
use SnowBuilds\SeederReset\Faker\FormatProvider;

class SeederResetServiceProvider extends ServiceProvider
{
    /**
     * Register the package's services.
     */
    public function register(): void
    {
        $this->app->singleton(SeederReset::class);
    }

    /**
     * Bootstrap the package's services.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerPublishing();
        SeederReset::boot();
    }

    /**
     * Register the package's commands.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                //
            ]);
        }
    }

    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('seeder-reset.php'),
            ], 'config');
        }
    }

}
