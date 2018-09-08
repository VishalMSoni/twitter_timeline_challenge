<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailable extends Mailable
{
    use Queueable, SerializesModels;
    public $name, $userMail, $xml_file;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData=[])
    {
        $this->name = $mailData['name'];
        $this->userMail = $mailData['user_mail'];
        $this->xml_file = $mailData['xml_file'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('vishalsoni611@gmail.com')
            ->view('email.name')
            ->subject('XML file attached !!!')
            ->attach(public_path($this->xml_file));
    }
}
