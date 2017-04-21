<?php

namespace LaravelFlare\MailDebug;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use LaravelFlare\MailDebug\MailDebugManager;
use LaravelFlare\MailDebug\Providers\MailServiceProvider;
use LaravelFlare\MailDebug\Providers\RouteServiceProvider;

class MailDebugServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->publishConfig();

        $this->app->register(RouteServiceProvider::class);
        $this->app->register(MailServiceProvider::class);
    }

    /**
     * Register any package services.
     */
    public function register()
    {
        $this->mergeConfig();

        $this->registerMailDebugManager();
    }

    /**
     * Register the Mail Debug instance.
     */
    private function registerMailDebugManager()
    {
        $this->app->singleton('flare.mailDebugManager', function ($app) {
            return new MailDebugManager(
                $app->make(Filesystem::class)
            );
        });
    }

    /**
     * Publishes the Mail Debug Config File.
     */
    private function publishConfig()
    {
        $this->publishes([
            $this->basePath('config/mail-debug.php') => config_path('mail-debug.php'),
        ]);
    }

    /**
     * Merge Configuration with Base Config.
     */
    private function mergeConfig()
    {
        $this->mergeConfigFrom(
            $this->basePath('config/mail-debug.php'), 'mail-debug'
        );
    }

    /**
     * Returns the path to a provided file within the Flare package.
     * 
     * @param bool $fiepath
     * 
     * @return string
     */
    private function basePath($filepath = false)
    {
        return __DIR__.'/../'.$filepath;
    }
}
