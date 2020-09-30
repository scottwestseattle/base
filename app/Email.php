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

    static public function sendPasswordReset(User $user)
    {
		$parms['subject'] = Lang::get('ui.Reset Password');
		$parms['link'] = 'https://' . domainName() . '/password/reset/' . $user->id . '/' . $user->password_reset_token;
		return self::send($user, $parms);
	}

    static public function sendVerification(User $user)
    {
		$parms['subject'] = Lang::get('ui.Email Verification');
		$parms['link'] = 'https://' . domainName() . '/users/verify-email/' . $user->id . '/' . $user->email_verification_token;
		return self::send($user, $parms);
	}
	
    static public function send(User $user, $parms)
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
			$email->subject = $parms['subject'];
			$email->link = $parms['link'];

			Mail::to($addressTo)->send($email);

			$msg = Lang::get('flash.Email has been sent') . ': ' . $msg;
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
