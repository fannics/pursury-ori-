<?php namespace ProjectCarrasco\Http\Middleware;

use Closure;

class LocalizationMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		if ($request->input('lang')){
			\App::setLocale($request->input('lang'));
		}

		return $next($request);
	}

}
