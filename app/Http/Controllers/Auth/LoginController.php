<?php

namespace ProjectCarrasco\Http\Controllers\Auth;

use ProjectCarrasco\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use ProjectCarrasco\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Lang;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected $redirectPath = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    public function loginPath()
    {
        return \Config::get('app')['route_prefix'].'/auth/login';
    }

    public function postLogin(\Illuminate\Http\Request $request)
    {

        $this->validate($request, [
            'email' => 'required|email', 'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials, $request->has('remember')))
        {
            $user = \Auth::user();

            if (!$user->active){

                auth()->logout();

                if (!$user->activate_token){
                    $user->activate_token = md5(sha1($user->email.'{'.$user->name.'}'.time()));
                    $user->save();
                }

                return view('auth/activation', ['user' => $user]);
            }

            return redirect()->intended($this->redirectPath());
        }

        return redirect($this->loginPath())
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => $this->getFailedLoginMessage(),
            ]);
    }


    public function getLogin(\Illuminate\Http\Request $request)
    {
        return view('auth/login', ['email' => $request->input('email')]);
    }

    public function getLogout()
    {
        auth()->logout();

        return redirect(property_exists($this, 'redirectAfterLogout') ? prefixed_route($this->redirectAfterLogout) : route('homepage'));
    }

    public function getCheckEmail(\Illuminate\Http\Request $request){

        try{
            $user = User::query()->where('email', $request->input('email'))->firstOrFail();
            die('false');
        } catch (ModelNotFoundException $e){
            die('true');
        }

    }

    public function getFailedLoginMessage()
    {

        return Lang::has('auth.failed')
            ? Lang::get('auth.failed')
            : 'These credentials do not match our records.';
    }
}