<?php namespace Secret\LaravelAB;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{

    public function provides()
    {
        return ['ab'];
    }


    public function register()
    {
        $this->app->singleton('ab', function ($app) {
            $ab = new LaravelAB($app);
            return $ab;
        }
        );
    }


    public function boot()
    {
        $configFile = __DIR__ . '/../config/ab.php';

        $this->mergeConfigFrom($configFile, 'ab');

        $this->publishes([
            $configFile => config_path('ab.php')
        ]);
    }

}
