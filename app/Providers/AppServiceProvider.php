<?php namespace ProjectCarrasco\Providers;

use Schema;
use Illuminate\Support\ServiceProvider;
use ProjectCarrasco\Setup;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
        if ( Schema::hasTable('setups'))
       {
           view()->share('currentSetups', Setup::orderBy('country', 'asc')->orderBy('language', 'asc')->get());

       }
  }

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
        if ($this->app->environment('production')) {
            $this->app->register(\Jenssegers\Rollbar\RollbarServiceProvider::class);
        }
	}

}
