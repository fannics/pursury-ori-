<?php
namespace ProjectCarrasco\Http\Controllers\Auth;

use ProjectCarrasco\Http\Controllers\Controller;
use ProjectCarrasco\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivationController extends Controller
{
    protected $redirectPath = '/';

    public function getActivation(Request $request, $token){

        try{

            $user = User::findOneByActivationToken($token);

            $user->setAttribute('activate_token', null);
            $user->setAttribute('active', true);

            $user->save();

            auth()->login($user);

            \Session::flash('success', trans('flash.account_activated_successfully') );

            return redirect($this->redirectPath());

        } catch (ModelNotFoundException $e){

            \Session::flash('error', trans('flash.error.invalid_activation_link') );

            return redirect(route('homepage'));
        }
    }

    public function redirectPath()
    {

            if (\Auth::getUser() && \Auth::getUser()->getAttribute('role') == 'ROLE_ADMIN'){

                return route('admin_home');
            }

            if (property_exists($this, 'redirectPath'))
            {
                return prefixed_route($this->redirectPath);
            }

            return property_exists($this, 'redirectTo') ? prefixed_route($this->redirectTo) : prefixed_route('/');
    }

    public function getActivationNeeded(\Illuminate\Http\Request $request){
        if ($request->input('key')){

            try{

                $user = User::findOneByActivationToken($request->input('key'));

                if ($user){

                    \Mail::send('emails/activation', ['activation_link' => route('get_activation', ['token' => $user->activate_token], true)], function($message) use ($user){
                        $message->to($user->email, $user->name)->subject(\Config::get('app')['app_title'].' - '. 'Email de activación');
                    });

                    return new JsonResponse(array(
                        'status' => 'success',
                        'message' => trans('json.mailSent')
                    ));

                } else {
                    return new JsonResponse(array(
                        'status' => 'fail',
                        'message' => trans('json.invalidRequest')
                    ));
                }

            } catch (\Exception $e){
                return new JsonResponse(array(
                    'status' => 'fail'
                ));
            }

        } else {
            return new JsonResponse(array(
                'status' => 'fail',
                'message' => trans('json.activationTokenRequired')
            ));
        }
    }

}