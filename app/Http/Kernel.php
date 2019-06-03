<?php namespace ProjectCarrasco\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

	/**
	 * The application's global HTTP middleware stack.
	 *
	 * @var array
	 */
	protected $middleware = [
		'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
		'Illuminate\Cookie\Middleware\EncryptCookies',
		'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
		'Illuminate\Session\Middleware\StartSession',
		'Illuminate\View\Middleware\ShareErrorsFromSession',
		'ProjectCarrasco\Http\Middleware\VerifyCsrfToken',

		'ProjectCarrasco\Http\Middleware\LocalizationMiddleware'
	];

	/**
	 * The application's route middleware.
	 *
	 * @var array
	 */
	protected $routeMiddleware = [
		'auth' => 'ProjectCarrasco\Http\Middleware\Authenticate',
		'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
		'guest' => 'ProjectCarrasco\Http\Middleware\RedirectIfAuthenticated',
		'is_admin' => 'ProjectCarrasco\Http\Middleware\RoleMiddleware',
		'id_inserter' => 'ProjectCarrasco\Http\Middleware\IdInserter'
	];

}
