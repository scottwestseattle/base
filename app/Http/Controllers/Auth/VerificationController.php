<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use Config;
use Log;

use App\User;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
        //$this->middleware('signed')->only('verify');
        //$this->middleware('throttle:6,1')->only('verify', 'resend');
    }
	
    public function sendVerificationEmail(Request $request, User $user)
    {

    }

    public function verifyEmail(Request $request, User $user, $token)
    {
		if ($user->email_verification_token == $token)
		{
			$user->user_type = Config::get('constants.user_type.confirmed');
			$user->save();
			
			logInfo('user email verified: ' . $user->email, 'email address verified, please log-in');
		}
		else
		{			
			logWarning('verifyEmail: tokens do not match: ' . $request->email, 'Email not verified - invalid link');
		}
		
		redirect('/login');
    }
	
}
