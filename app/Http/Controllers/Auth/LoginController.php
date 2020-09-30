<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

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
		]);		

        $this->middleware('owner')->only([
			'resetPassword', 'editPassword', 'updatePassword',
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
	
	public function resetPassword(Request $request, User $user)
    {
		$token = uniqueToken();
		return view('auth.passwords.reset', ['record' => $user, 'token' => $token]);
	}

	public function editPassword(Request $request, User $user)
    {		
		$token = uniqueToken();
		return view('auth.passwords.reset', ['record' => $user, 'token' => $token]);
	}

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
		dd('here');
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    public function updatePassword(Request $request, User $user)
    {		
		if ($request->password != $request->password_confirmation)
		{
			// don't match
			Log::warning('Password Reset - Password does not match confirmation', ['id' => $user->id]);
			flash('warning', 'New password and password confirmation do not match');
			return back();
		}
        else if (Auth::attempt(['email' => $user->email, 'password' => $request->current_password])) 
		{
			if (User::isBlocked())
			{
				flash('warning', 'User is Blocked');
				Log::warning('Blocked user change password attempt: ' . $request->email);
				return back();
			}
			else
			{
				// Authentication passed...
				$user->password = Hash::make($request->password);
				$user->save();
				Log::info('User password updated', ['id' => $user->id]);
				flash('success', 'Password updated');
			}			
        }
		else
		{
			logWarning('Password reset: current password invalid: ' . $request->email, 'Current password invalid');
			return back();
		}
			
		return redirect('/users/view/{{$user->id}}'); 
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
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) 
		{
			if (User::isUserBlocked())
			{
				flash('warning', 'User is Blocked');
				Log::warning('Blocked user login attempt: ' . $request->email);
			}
			else
			{
				// Authentication passed...
				return redirect()->intended('dashboard');
			}			
        }
		else
		{
			flash('warning', 'Invalid credentials');
			Log::warning('invalid login credentials: ' . $request->email);
		}
		
		return redirect('/login');
    }
}