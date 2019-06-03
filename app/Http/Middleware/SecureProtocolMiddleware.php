<?php namespace ProjectCarrasco\Http\Middleware;

use Closure;

class SecureProtocolMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
  
		if (!$request->secure() && env('APP_ENV') != 'development') {
			return redirect()->secure($request->getRequestUri());
		}
    
		return $next($request);
	}

}
