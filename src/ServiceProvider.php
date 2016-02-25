<?php namespace Websecret\LaravelAB;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{

    protected $defer = false;

    public function provides()
    {
        return ['ab'];
    }


    public function register()
    {
        $this->app->singleton('ab', function () {
            $ab = new LaravelAB();
            return $ab;
        });
    }


    public function boot()
    {
        $configFile = __DIR__ . '/../config/ab.php';

        $this->mergeConfigFrom($configFile, 'ab');

        $this->publishes([
            $configFile => config_path('ab.php')
        ]);

        $this->registerMiddleware('Websecret\LaravelAB\Middleware\LaravelAB');
    }

    protected function registerMiddleware($middleware)
    {
        $kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
        $kernel->pushMiddleware($middleware);
    }

}
