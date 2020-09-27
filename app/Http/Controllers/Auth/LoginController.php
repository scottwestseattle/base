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
		$token = '';
		
		return view('auth.passwords.reset', ['token' => $token]);		
	}

	public function editPassword(Request $request, User $user)
    {
		$token = '';
		
		return view('auth.passwords.edit', ['token' => $token]);		
	}

	public function updatePassword(Request $request, User $user)
    {
		return redirect('/');		
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
			if (User::isBlocked())
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