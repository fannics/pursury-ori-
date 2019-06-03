<?php namespace ProjectCarrasco\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IdInserter{

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		$action = $request->route()->getAction();

		if (isset($action['ref_id'])){
			$request->attributes->set('ref_id', $action['ref_id']);
			return $next($request);
		} else {
			throw new NotFoundHttpException();
		}
	}

}
