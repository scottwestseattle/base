<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailable;

use Auth;
use Lang;
use Log;

use App\User;

class Email extends Model
{
    use HasFactory;
	
    static public function sendVerification(User $user)
    {
		$name = $user->name;
		$addressTo = $user->email;
		$addressFrom = env('MAIL_FROM_ADDRESS', '63f42e54a4-f10d4b@inbox.mailtrap.io');
		$to = Lang::get('content.To');
		$from = Lang::get('content.From');
		$rc = false;

		//
		// send the email
		//
		// From: from@mail.com, To: to@mail.com
		$msg = $from . ': ' . $addressFrom . ', ' . $to . ': ' . $addressTo . ', email verification link';
		try
		{
			$email = new SendMailable($name);
			$email->subject = Lang::get('ui.Email Verification');

			$d = 'https://' . domainName();
			$email->link = $d . '/users/verify-email/' . $user->id . '/' . $user->email_verification_token;

			Mail::to($addressTo)->send($email);

			$msg = Lang::get('flash.Email has been sent') . ': ' . $msg;
			logInfo($msg, 'Email successfully sent');
			$rc = true;
		}
		catch (\Exception $e)
		{
			$flash = Lang::get('flash.Error sending email'); 
			$msg = $flash . ': ' . $e->getMessage();
			flash('danger', $flash);
			Log::emergency('manual send - error sending email verification: ' . $e->getMessage());			
		}
		
		return $rc;
    }
}
