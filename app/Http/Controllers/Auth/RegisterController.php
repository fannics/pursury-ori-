<?php
namespace ProjectCarrasco\Http\Controllers\Auth;

use ProjectCarrasco\User;
use ProjectCarrasco\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use ProjectCarrasco\Http\Requests\RegisterFormRequest;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */
    use RegistersUsers;
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
            'gender' => 'required'
        ]);
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        //all users will be created with the front user role
        $default_role = 'ROLE_FRONT_USER';

        $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'gender' => $data['gender'],
            'role' => $default_role,
            'active' => false,
            'activate_token' => md5(sha1($data['email'].'{'.$data['name'].'}'.time())),
            'newsletter' => isset($data['newsletter'])
        ]);

        return $user;
    }

    public function postRegister(RegisterFormRequest $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails())
        {
            $this->throwValidationException(
                $request, $validator
            );
        }

        try{

            $user = $this->create($request->all());

            //send activation mail to the user
            \Mail::send('emails/activation', ['activation_link' => route('get_activation', ['token' => $user->activate_token], true)], function($message) use ($user){
                $message->to($user->email, $user->name)->subject(\Config::get('app')['app_title'].' - '. trans('emails.welcome_our_site') ) ;
            });

            \Session::flash('success', trans('flash.account_created_successfully') );

            return redirect($this->redirectPath());

        } catch(\Exception $e){
            \Session::flash('error', trans('flash.error.creating_account_error'));

            return redirect(prefixed_route('/auth/register'));
        }
    }
}