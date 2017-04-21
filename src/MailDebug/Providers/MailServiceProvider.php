<?php

namespace LaravelFlare\MailDebug\Providers;

use Swift_Mailer;
use LaravelFlare\MailDebug\DebugTransport;
use LaravelFlare\MailDebug\MailDebugManager;
use Illuminate\Mail\MailServiceProvider as ServiceProvider;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Register the Swift Mailer instance.
     */
    public function registerSwiftMailer()
    {
        if ($this->app['config']['mail.driver'] != 'debug') {
            parent::registerSwiftMailer();

            return;
        }

        $this->app->singleton('swift.mailer', function ($app) {
            return new Swift_Mailer(
                new DebugTransport(
                    $app->make(MailDebugManager::class)
                )
            );
        });
    }
}
