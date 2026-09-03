<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppConnectionDetails extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $appConfig;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($appConfig)
    {
        $this->appConfig = $appConfig;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Application Connection Details - ' . $this->appConfig->appName)
                    ->view('emails.app_connection_details');
    }
}
