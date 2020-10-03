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
	
	public function editPassword(Request $request, User $user)
    {		
		$token = uniqueToken();
		return view('auth.passwords.reset', ['user' => $user, 'token' => $token]);
	}

    public function updatePassword(Request $request, User $user)
    {
		$data = $request->validate([
			'current_password' => 'required|string|min:8|max:25',		
			'password' => 'required|string|min:8|max:25|confirmed',		
		]);	
	
		if (Auth::attempt(['email' => $user->email, 'password' => $data['current_password']])) 
		{
			if ($user->isBlocked())
			{
				logWarning(__FUNCTION__, 'User is blocked', ['email' => $user->email]);
				return back();
			}
			else
			{
				// Authentication passed...
				$user->password = Hash::make($data['password']);
				$user->save();
				logInfo(__FUNCTION__, 'Password updated', ['email' => $user->email]);
			}			
        }
		else
		{
			logWarning(__FUNCTION__, 'Current password invalid');
			return back();
		}
			
		return redirect("/users/view/$user->id"); 
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
				logWarning(__FUNCTION__, 'User is Blocked', ['email' => $data['email']]);
			}
			else
			{
				// Authentication passed...
				return redirect()->intended('dashboard');
			}			
        }
		else
		{
			logWarning(__FUNCTION__, 'Invalid credentials', ['email' => $data['email']]);
		}
		
		return redirect('/login');
    }
}