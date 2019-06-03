<?php

namespace ProjectCarrasco\Http\Controllers\Auth;

use ProjectCarrasco\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\Request;
use ProjectCarrasco\Http\Requests\RecoverPasswordFormRequest;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    protected $redirectPath = '/';


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PasswordBroker $passwords)
    {
        $this->passwords = $passwords;

        $this->middleware('guest');
    }

    public function postEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $response = $this->passwords->sendResetLink($request->only('email'), function($m)
        {
            $m->subject(settings('app.app_title').' - '. trans('emails.recover_password') );
        });

        switch ($response)
        {
            case PasswordBroker::RESET_LINK_SENT:

                \Session::flash('success', trans('flash.email_recover_password') );

                return redirect()->back()->with('status', trans($response));
            case PasswordBroker::INVALID_USER:

                return redirect()->back()->withErrors(['email' => trans($response)]);
        }
    }

    public function postReset(RecoverPasswordFormRequest $request)
    {

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = $this->passwords->reset($credentials, function($user, $password)
        {
            $user->password = bcrypt($password);

            $user->save();

            auth()->login($user);
        });

        switch ($response)
        {
            case PasswordBroker::PASSWORD_RESET:
                return redirect($this->redirectPath());

            default:
                return redirect()->back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => trans($response)]);
        }
    }

    public function redirectPath()
    {
            if (\Auth::getUser()->getAttribute('role') == 'ROLE_ADMIN')
            {
                return route('admin_home');
            }

            if (property_exists($this, 'redirectPath'))
            {
                return $this->redirectPath;
            }

            return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
    }
}