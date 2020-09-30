<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DateTime;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
		
		parent::__construct();
    }
	
    public function reset(Request $request, $token = null)
    {
		if (isset($token))
		{
			// clicked on password reset email link
			
		}
		else
		{
			// form where user enters the email address
			return view('auth.passwords.request-password-reset');
		}			
		
		return redirect($this->$redirectTo);
    }	
	
    public function sendPasswordReset(Request $request)
    {
		// look up user by email address
		$user = User::getByEmail($request->email);
		
		if (isset($user))
		{
			// save the password reset token with the user
			$token = uniqueToken();
			$user->password_reset_token = $token;
			$user->password_reset_token_expiration = DateTime::getTimeStamp();
			$user->save();
			
			// send the token in an email
			Email::sendPasswordReset($user);
		}
				
		return view('auth.passwords.reset-email-sent', ['email' => $request->email]);
    }	
	
}
