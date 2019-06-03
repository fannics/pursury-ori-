<?php namespace ProjectCarrasco\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use ProjectCarrasco\Setup;
use Schema;

class RouteServiceProvider extends ServiceProvider {

	/**
	 * This namespace is applied to the controller routes in your routes file.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'ProjectCarrasco\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function boot()
	{
		parent::boot();

		//
	}

	/**
	 * Define the routes for the application.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function map(Router $router)
	{
	    if (Schema::hasTable('setups') && Setup::count() != 0)
        {
            $router->group(['namespace' => $this->namespace], function($router)
            {
                require app_path('Http/routes.php');

//			if (file_exists(storage_path('app/category_routes.php'))){
//				require storage_path('app/category_routes.php');
//			}

                if (file_exists(app_path('/Http/fallback_route.php'))){
                    require app_path('/Http/fallback_route.php');
                }

//			if (file_exists(storage_path('app/product_routes.php'))){
//				require storage_path('app/product_routes.php');
//			}
            });
        }

	}

}
