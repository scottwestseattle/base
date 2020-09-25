<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
	public function __construct ()
	{			
		parent::__construct();
	}	
	
	public function login(Request $request)
    {
		return view('auth.login');		
	}

	public function logout(Request $request)
    {
		Auth::logout();
		
		return redirect('/');
	}
	
	public function register(Request $request)
    {
		return view('auth.register');		
	}

	public function resetPassword(Request $request)
    {
		$token = '';
		
		return view('auth.passwords.reset', ['token' => $token]);		
	}

	public function updatePassword(Request $request)
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
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) 
		{
            // Authentication passed...
            return redirect()->intended('dashboard');
        }
		else
		{
			dump('invalid credentials');
		}
    }
}