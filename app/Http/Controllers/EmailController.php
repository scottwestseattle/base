<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailable;

use Auth;
use Lang;
use Log;

use App\User;

class EmailController extends Controller
{	
	public function __construct ()
	{
		parent::__construct();
	}

	public function send(Request $request)
	{
		$user = Auth::user();
		
		$name = $user->name;
		$addressTo = $user->email;
		$addressFrom = env('MAIL_FROM_ADDRESS', '63f42e54a4-f10d4b@inbox.mailtrap.io');
		$to = Lang::get('content.To');
		$from = Lang::get('content.From');
		$debug = false;

		//
		// send the email
		//
		// From: from@mail.com, To: to@mail.com
		$msg = $from . ': ' . $addressFrom . ', ' . $to . ': ' . $addressTo . ', email verification link';
		try
		{
			$email = new SendMailable($name);

			$email->subject = Lang::get('content.Email Verification');

			//$d = 'https://' . domainName();
			$d = 'localhost';
			$email->link = $d . '/verify-email/{{$user->email}}/token-goes-here';

			if (!$debug)
				Mail::to($addressTo)->send($email);

			$msg = Lang::get('flash.Email has been sent') . ': ' . $msg;
			logInfo($msg, $msg);
		}
		catch (\Exception $e)
		{
			$flash = Lang::get('flash.Error sending email'); 
			$msg = $flash . ': ' . $e->getMessage();
			logError($msg, $flash);
		}
	}
}
