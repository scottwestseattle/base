<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailable;

use Auth;
use Lang;
use Log;

use App\Email;
use App\User;

class EmailController extends Controller
{	
	public function __construct ()
	{
        $this->middleware('auth')->except([
		]);
		
		parent::__construct();
	}

	public function send(Request $request, User $user)
	{
		try
		{
			if (blank($user->email_verification_token))
			{
				$user->email_verification_token = self::getToken();
				$user->save();
			}

			if (!Email::sendVerification($user))
				throw new \Exception("error sending email");
			
			return view('email.verification-email-sent', [
				'user' => $user,
			]);
		}
		catch(\Exception $e)
		{
			Log::emergency('manual send email error - ' . $e->getMessage());
			return view('email.verification-email-not-sent', [
				'user' => $user,
			]);			
		}

	}
}
