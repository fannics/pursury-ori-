<?php namespace ProjectCarrasco\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class RoleMiddleware {

	protected $auth;

	function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}


	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		if (!$this->auth->user() || !($this->auth->user()->role == 'ROLE_ADMIN')) {

			if ($request->ajax())
			{
				return response('Unauthorized.', 401);
			}
			else
			{

				$sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
        $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
        $prefix = \Config::get('app')['route_prefix']. $sessionCountry.$sessionLanguage;

				return redirect()->guest($prefix.'/auth/login');
			}

		} else {
			return $next($request);
		}
	}

}
