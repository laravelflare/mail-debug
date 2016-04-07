<?php

namespace LaravelFlare\MailDebug\Providers;

use Illuminate\Routing\Router;
use Illuminate\Contracts\Http\Kernel;
use LaravelFlare\MailDebug\MailDebugManager;
use LaravelFlare\MailDebug\Http\Middleware\MailDebug;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace;

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function boot(Router $router)
    {
        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function map(Router $router)
    {
        $this->registerRoutes($router);
        $this->registerMiddleware();
    }

    /**
     * Register the Mail Debug Middleware.
     *
     * The Mail Debug Middleware checks the Session on every request
     * and if an email was saved as a preview in the previous request,
     * the middleware will append the pop up content and javascript
     * to the end of the resulting page request response.
     */
    protected function registerMiddleware()
    {
        $this->app[Kernel::class]->pushMiddleware(MailDebug::class);
    }

    /**
     * Register the Mail Debug Routes.
     * 
     * @param Router $router
     */
    protected function registerRoutes(Router $router)
    {
        $debug = $this->app->make(MailDebugManager::class);

        $router->get('mail-debug/{file}', function($file) use ($debug) {
            if (file_exists($path = $debug->storage().'/'.$file)) {
                return include($path);
            }

            return abort(404);
        })->name('mail-debug');
    }
}
