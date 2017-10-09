<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the Facebook/Google authentication page.
     *
     * @return Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver(strtolower($provider))->redirect();
    }

    /**
     * Obtain the user information from Facebook/Google.
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {
        switch (strtolower($provider)) {
            case 'google':
                $socialiteUser = Socialite::driver($provider)->stateless()->user();
                break;
            
            default:
                $socialiteUser = Socialite::driver($provider)->user();
                break;
        }
        

        $findUser = User::where('email',$socialiteUser->email)->first();

        if ($findUser) {
            Auth::login($findUser);
            return "Old User";

        }else{

            $user = new User();
            $user->name = $socialiteUser->name;
            $user->email = $socialiteUser->email;
            $user->password = bcrypt(123456);
            $user->save();

            Auth::login($user);

            return "New user";
        }
    }
}
