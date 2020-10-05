<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailable;

use App;
use Auth;
use Lang;
use Log;

use App\User;

class Email extends Model
{
    use HasFactory;

    static public function sendPasswordReset(User $user)
    {
		$locale = App::getLocale();
		$parms['subject'] = Lang::get('base.Reset Password');
		$parms['link'] = 'https://' . domainName() . '/' . $locale . '/password/reset/' . $user->id . '/' . $user->password_reset_token;
		$parms['linkText'] = 'Please click here to reset your password';
		
		return self::send($user, $parms);
	}

    static public function sendVerification(User $user)
    {
		$parms['subject'] = Lang::get('base.Email Verification');
		$parms['link'] = 'https://' . domainName() . '/users/verify-email/' . $user->id . '/' . $user->email_verification_token;
		$parms['linkText'] = 'Please click here to verify your email address';
		return self::send($user, $parms);
	}
	
    static public function send(User $user, $parms)
    {
		$name = $user->name;
		$addressTo = $user->email;
		$addressFrom = env('MAIL_FROM_ADDRESS', '63f42e54a4-f10d4b@inbox.mailtrap.io');
		$to = Lang::get('base.To');
		$from = Lang::get('base.From');
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
			$email->linkText = $parms['linkText'];

			Mail::to($addressTo)->send($email);

			$msg = Lang::get('base.Email has been sent') . ': ' . $msg;
			$rc = true;
		}
		catch (\Exception $e)
		{
			$flash = Lang::get('base.Error sending email'); 
			$msg = $flash . ': ' . $e->getMessage();
			flash('danger', $flash);
			Log::emergency('manual send - error sending email verification: ' . $e->getMessage());			
		}
		
		return $rc;
    }
}
