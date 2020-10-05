<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DateTime;
use Hash;
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
					$msg = __('base.Password reset link has expired, please try again');
					Log::warning($msg, ['expiration' => $user->password_reset_expiration, 'now' => new DateTime('NOW')]);
					flash('warning', $msg);
					
					$user->password_reset_token = null;
					$user->password_reset_expiration = null;
					$user->save();

					return redirect('/password/request-reset');
				}
				else
				{					
					// go to reset password form
					return view('auth.passwords.reset', ['user' => $user]);
				}

			}
			else
			{
				logWarning(__FUNCTION__, __('base.Password reset link has already been used'));
			}
		}
		else
		{
			logWarning(__FUNCTION__, __('base.Password reset link is invalid'));
		}
	
		return redirect($this->redirectTo);
    }	
	
    public function sendPasswordReset(Request $request)
    {
		$email = alphanumpunct($request->email);
		if ($email != $request->email)
		{
			// abort
			logWarning(__FUNCTION__ . ' - email address has funky characters: ' . $request->email);
			return view('auth.passwords.reset-email-not-sent');
		}
		
		// look up user by email address
		$user = User::getByEmail($email);
		
		if (isset($user))
		{
			if ($user->isBlocked())
			{
				logWarning(__FUNCTION__ . ' - user is blocked');
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
					logInfo(__FUNCTION__ . ' - token saved to user record');
					
					// send the token in an email
					if (Email::sendPasswordReset($user))
					{
						logInfo(__FUNCTION__ . ' - email sent');
					}
					else
					{
						logError(__FUNCTION__ . ' - error sending email: ' . $email);
					}
				}
				catch(\Exception $e)
				{
					logError(__FUNCTION__ . ', exception, email: ' . $email . ', ' . $e->getMessage());
				}
			}
		}
		else
		{
			logWarning(__FUNCTION__ . ' - email not found: ' . $email);
		}
				
		return view('auth.passwords.reset-email-sent', ['email' => $email]);
    }

    public function updatePassword(Request $request, User $user)
    {
		$data = $request->validate([
			'password' => 'required|string|min:8|max:25|confirmed',		
		]);	

		if ($user->isBlocked())
		{
			logWarning(__FUNCTION__, __('base.User is blocked'), ['email' => $user->email]);
			return back();
		}
		else
		{
			try
			{
				// Authentication passed, save the new password
				$user->password_reset_token = null;
				$user->password_reset_expiration = null;
				$user->password = Hash::make($data['password']);
				$user->save();
				logInfo(__FUNCTION__, __('base.Password has been reset, please log-in'), ['email' => $user->email]);
			}
			catch(\Exception $e)
			{
				logException(__FUNCTION__, $e->getMessage(), __('Error saving new password'));
				return back();
			}
		}
			
		return redirect('/login'); 
    }		
	
}
