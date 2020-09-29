<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailable extends Mailable
{
    use Queueable, SerializesModels;
	public $name;
	public $title;
	public $link;

/*
	$from		The person the message is from.	
	$to			The "to" recipients of the message.	
	$cc			The "cc" recipients of the message.	
	$bcc		The "bcc" recipients of the message.	
	$replyTo	The "reply to" recipients of the message.	
	$subject	The subject of the message.				
*/
	
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name)
    {
		$this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		$this->title = $this->subject;
		
        return $this
			->from(env('MAIL_FROM_ADDRESS', '63f42e54a4-f10d4b@inbox.mailtrap.io'))
			->view('email.verify-email');
    }
}
