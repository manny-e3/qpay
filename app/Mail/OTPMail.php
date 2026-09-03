<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OTPMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    // public $otp;
    public $info;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($info)
    {
        // $this->otp = $otp;
        $this->info = $info;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {


        return $this->subject($this->info['subject'])
            ->from($this->info['from'], $this->info['app'])
            ->view('emails.otp')
            ->with([
                'info' => $this->info,
            ]);
    }
}