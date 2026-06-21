<?php

namespace Shearerline;

use Illuminate\Support\ServiceProvider;
use Shearerline\Contracts\ShearerlineInterface;

class ShearerlineServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/shearerline.php',
            'shearerline'
        );

        $this->app->singleton('shearerline', function ($app) {
            return new Shearerline();
        });

        $this->app->bind(ShearerlineInterface::class, Shearerline::class);

        $this->registerCommands();
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/shearerline.php' => config_path('shearerline.php'),
            ], 'shearerline-config');

            $this->publishes([
                __DIR__ . '/database/migrations' => database_path('migrations'),
            ], 'shearerline-migrations');

            $this->publishes([
                __DIR__ . '/database/seeds' => database_path('seeders'),
            ], 'shearerline-seeds');

            $this->publishes([
                __DIR__ . '/resources/views' => resource_path('views/vendor/shearerline'),
            ], 'shearerline-views');

            $this->publishes([
                __DIR__ . '/resources/js' => resource_path('js/vendor/shearerline'),
            ], 'shearerline-assets');

            $this->publishes([
                __DIR__ . '/resources/lang' => $this->app->langPath('vendor/shearerline'),
            ], 'shearerline-lang');
        }

        $this->registerRoutes();
        $this->registerViews();
        $this->registerMigrations();
    }

    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
    }

    protected function registerViews(): void
    {
        if (config('shearerline.views.enabled', true)) {
            $this->loadViewsFrom(__DIR__ . '/resources/views', 'shearerline');
        }
    }

    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Shearerline\Console\Commands\InstallCommand::class,
            ]);
        }
    }
}
