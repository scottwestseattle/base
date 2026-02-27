<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Hash;
use Log;

use App\User;

class LoginController extends Controller
{
	public function __construct ()
	{
        $this->middleware('auth')->except([
			'login',
			'logout',
			'authenticate',
			'updatePassword',
		]);

        $this->middleware('owner')->only([
			'resetPassword', 'editPassword',
		]);

		parent::__construct();
	}

	public function login(Request $request)
    {
		Auth::logout();

		return view('auth.login');
	}

	public function logout(Request $request)
    {
		Auth::logout();

		return redirect('/');
	}

	public function editPassword(Request $request, $locale, User $user)
    {
		$token = uniqueToken();
		return view('auth.passwords.edit', ['user' => $user, 'token' => $token]);
	}

    public function updatePassword(Request $request, $locale, User $user)
    {
		$data = $request->validate([
			'current_password' => 'required|string|min:8|max:25',
			'password' => 'required|string|min:8|max:25|confirmed',
		]);

		if (Auth::attempt(['email' => $user->email, 'password' => $data['current_password']]))
		{
			// remove the password reset token
			try
			{
				$user->password_reset_token = null;
				$user->password_reset_expiration = null;
				$user->save();
			}
			catch(\Exception $e)
			{
				logException(__('Error removing password reset token'), $e->getMessage());
			}

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
					$user->password = Hash::make($data['password']);
					$user->save();
					logInfo(__FUNCTION__, __('base.Password updated'), ['email' => $user->email]);
				}
				catch(\Exception $e)
				{
					logException(__FUNCTION__, $e->getMessage(), __('Error saving new password'));
				}
			}
        }
		else
		{
			logWarning(__FUNCTION__, __('base.Current password invalid'));
			return back();
		}

		return redirect(route('login', ['locale' => $locale]));
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function authenticate(Request $request)
    {
		$data = $request->validate([
			'email' => 'required|email',
			'password' => 'required|string',
		]);

        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']]))
		{
			if (User::isUserBlocked())
			{
				logWarning(__FUNCTION__, __('base.User is Blocked'), ['email' => $data['email']]);
			}
        	if (!User::isConfirmed())
			{
			    $msg = __('base.User email has not been confirmed') . ': ';
    			//$msg = '<a href="/email/send/{{Auth::id()}}">__("proj.Resend the confirmation email"): Auth::user()->email</a>';
    			$msg .= "<a target='_blank' href='/email/send/" . Auth::id() . "'>" . __('proj.Resend the confirmation email') . "</a>";
				flash('warning', $msg);
				//logError(__FUNCTION__, $msg, ['email' => $data['email']]);
			}
			else
			{
				// Authentication passed...
				return redirect()->intended(lurl('dashboard'));
			}
        }
		else
		{
			logWarning(__FUNCTION__, __('base.Invalid credentials'), ['email' => $data['email']]);
		}

		return redirect()->back();
    }
}
