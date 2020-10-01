<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DateTime;
use Log;

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
			if ($user->password_reset_token == $token)
			{				
				if (isExpired($user->password_reset_expiration))
				{
					$msg = 'Password reset link has expired, please try again';
					Log::warning($msg, ['expiration' => $user->password_reset_expiration, 'now' => new DateTime('NOW')]);
					flash('warning', $msg);
					
					$user->password_reset_token = null;
					$user->password_reset_expiration = null;
					$user->save();					
				}
				else
				{
					$user->password_reset_token = null;
					$user->password_reset_expiration = null;
					$user->save();
					
					// go to reset password form
					return view('auth.passwords.reset', ['user' => $user]);
				}

			}
			else
			{
				$msg = 'Password reset link has already been used';
				logWarning($msg, $msg);
			}
		}
		else
		{
			$msg = 'Password reset link is invalid';
			logWarning($msg, $msg);
		}
	
		return redirect($this->redirectTo);
    }	
	
    public function sendPasswordReset(Request $request)
    {
		$function = 'password reset email request';
		$email = alphanumpunct($request->email);
		if ($email != $request->email)
		{
			// abort
			logWarning($function . ' - email address has funky characters: ' . $request->email);
			return view('auth.passwords.reset-email-not-sent');
		}
		
		// look up user by email address
		$user = User::getByEmail($email);
		
		if (isset($user))
		{
			if ($user->isBlocked())
			{
				logWarning($function . ' - user is blocked');
			}
			else
			{
				try
				{
					// save the password reset token with the user
					$token = uniqueToken();
					$user->password_reset_token = $token;				
					$user->password_reset_expiration = getTimestampFuture(/* minutes = */ 30);
					$user->save();
					
					// send the token in an email
					if (Email::sendPasswordReset($user))
					{
						logInfo($function . ' - email sent');
					}
					else
					{
						logError($function . ' - error sending email: ' . $email);
					}
				}
				catch(\Exception $e)
				{
					logError($function . ' - error saving password request token or sending email: ' . $email);
				}
			}
		}
		else
		{
			logWarning($function . ' - email not found: ' . $email);
		}
				
		return view('auth.passwords.reset-email-sent', ['email' => $email]);
    }	
	
}
