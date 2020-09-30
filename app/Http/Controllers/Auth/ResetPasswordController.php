<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Email;
use App\User;

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
        //$this->middleware('guest');
		
		parent::__construct();
    }
	
    public function requestReset(Request $request)
    {
		return view('auth.passwords.request-reset');
    }	

    public function resetPassword(Request $request, User $user, $token)
    {
		$token = alphanum($token);
		if (isset($token))
		{
			// clicked on password reset email link
			
		}
	
		return redirect($this->$redirectTo);
    }	
	
    public function sendPasswordReset(Request $request)
    {
		$email = alphanumpunct($request->email);
		if ($email != $request->email)
		{
			// abort
			logWarning('password reset email request - email address has funky characters: ' . $request->email);
			return view('auth.passwords.reset-email-not-sent');
		}
		
		// look up user by email address
		$user = User::getByEmail($email);
		
		if (isset($user))
		{
			if ($user->isBlocked())
			{
				logWarning('password reset email request - user is blocked');
			}
			else
			{
				dd('not blocked');
				// save the password reset token with the user
				$token = uniqueToken();
				$user->password_reset_token = $token;				
				$user->password_reset_expiration = getTimestampFuture(/* minutes = */ 30);
				$user->save();
				
				// send the token in an email
				Email::sendPasswordReset($user);
			}
		}
		else
		{
			logWarning('password reset email request - email not found: ' . $email);
		}
				
		return view('auth.passwords.reset-email-sent', ['email' => $email]);
    }	
	
}
