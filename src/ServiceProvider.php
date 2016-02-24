<?php namespace Secret\LaravelAB;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['ab'];
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('ab', function ($app) {
			$ab = new LaravelAB($app);
			return $ab;
		}
		);
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$configFile = __DIR__ . '/../config/ab.php';

		$this->mergeConfigFrom($configFile, 'ab');

		$this->publishes([
			$configFile => config_path('ab.php')
		]);
	}

}
