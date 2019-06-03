<?php namespace ProjectCarrasco\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use ProjectCarrasco\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use ProjectCarrasco\User;

use Illuminate\Http\Request;

class ReloadedAuthController extends Controller {

	protected $redirectPath = '/';

	use AuthenticatesAndRegistersUsers;

	//Override of the register process
	public function postRegister(\Illuminate\Http\Request $request)
	{
		die ();

		$validator = $this->registrar->validator($request->all());

		if ($validator->fails())
		{
			$this->throwValidationException(
				$request, $validator
			);
		}

		try{

			$user = $this->registrar->create($request->all());

			//send activation mail to the user
			\Mail::send('emails/activation', ['token' => $user->getAttribute('activate_token')], function($message) use ($user){
				$message->to('andressolenzal@localhost', 'Andres')->subject(\Config::get('app')['app_title'].' - '. trans('emails.welcome_our_site') );
			});

			\Session::flash('success', trans('flash.account_created_successfully') );

			die ();

		} catch(\Exception $e){

			throw $e;

			\Session::flash('error', trans('flash.error.creating_account_error2') );
		}

		return redirect($this->redirectPath());
	}

	//Override of the redirect path after login or register
	public function redirectPath()
	{
		if (\Auth::getUser() && \Auth::getUser()->getAttribute('role') == 'ROLE_ADMIN'){

			return route('admin_home');

		} else {

			if (property_exists($this, 'redirectPath'))
			{
				return prefixed_route($this->redirectPath);
			}

			return property_exists($this, 'redirectTo') ? prefixed_route($this->redirectTo) : prefixed_route('/');
		}
	}

	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;

		$this->middleware('guest', ['except' => 'getLogout']);
	}

	public function getActivation(Request $request, $token){

		try{

			$user = User::findOneByActivationToken($token);

			$user->setAttribute('activate_token', null);
			$user->setAttribute('active', true);

			$user->save();

			$this->auth->login($user);

			\Session::flash('success', trans('flash.account_activated_successfully') );

			return redirect($this->redirectPath());

		} catch (ModelNotFoundException $e){

			\Session::flash('error', trans('error.invalid_activation_link') );

			return redirect($this->redirectPath());
		}
	}

	public function getCheckEmail(\Illuminate\Http\Request $request){

		try{
			$user = User::query()->where('email', $request->input('email'))->firstOrFail();
			die('false');
		} catch (ModelNotFoundException $e){
			die('true');
		}

	}

	public function loginPath()
	{
		return \Config::get('app')['route_prefix'].'/auth/login';
	}

	protected function getFailedLoginMessage()
	{
		return 'Lo sentimos, usuario o contraseña incorrectos';
	}


	/**
	 * Custom handling for the login action
	 */
	public function postLogin(\Illuminate\Http\Request $request)
	{
  
		$this->validate($request, [
			'email' => 'required|email', 'password' => 'required',
		]);

		$credentials = $request->only('email', 'password');

		if ($this->auth->attempt($credentials, $request->has('remember')))
		{
			return redirect()->intended($this->redirectPath());
		}
    
		return redirect($this->loginPath())
			->withInput($request->only('email', 'remember'))
			->withErrors([
				'email' => $this->getFailedLoginMessage(),
			]);
	}


}
