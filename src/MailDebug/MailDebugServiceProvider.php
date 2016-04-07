<?php

namespace LaravelFlare\MailDebug;

use Swift_Mailer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Mail\MailServiceProvider;
use LaravelFlare\MailDebug\MailDebugManager;
use LaravelFlare\MailDebug\Providers\RouteServiceProvider;

class MailDebugServiceProvider extends MailServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->publishConfig();
    }

    /**
     * Register any package services.
     */
    public function register()
    {
        parent::register();

        $this->app->register(RouteServiceProvider::class);

        $this->mergeConfig();

        $this->registerMailDebugManager();
    }

    /**
     * Register the Swift Mailer instance.
     *
     * @return void
     */
    public function registerSwiftMailer()
    {
        if ($this->app['config']['mail.driver'] != 'debug') {
            parent::registerSwiftMailer();

            return;
        }

        $this->app['swift.mailer'] = $this->app->share(function ($app) {
            return new Swift_Mailer(
                new DebugTransport(
                    $app->make(MailDebugManager::class)
                )
            );
        });
    }

    /**
     * Register the Mail Debug instance.
     *
     * @return void
     */
    private function registerMailDebugManager()
    {
        $this->app['flare.mailDebugManager'] = $this->app->share(function ($app) {
            return new MailDebug(
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
     * Merge Configuration with Base Config
     *
     * @return void
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
