<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
	public function __construct ()
	{			
        $this->middleware('auth')->except([
			'login',
			'logout',
			'authenticate',
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
	
	public function resetPassword(Request $request)
    {
		$token = '';
		
		return view('auth.passwords.reset', ['token' => $token]);		
	}

	public function editPassword(Request $request)
    {
		$token = '';
		
		return view('auth.passwords.edit', ['token' => $token]);		
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
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'blocked_flag' => 0])) 
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