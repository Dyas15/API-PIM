<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        $logoCaminho = base_path('public/images/logo.png');
        
        return $this->view('emails.reset_password')
                    ->subject('Seu link para redefinição de senha')
                    ->with(['token' => $this->token])
                    ->attach($logoCaminho, [
                        'as' => 'logo.png',
                        'mime' => 'image/png',
                    ]);
    }
}
