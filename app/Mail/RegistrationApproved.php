<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('emails.registration-approved')
                    ->subject('Votre compte a été approuvé - Car Tracking App')
                    ->with([
                        'userName' => $this->user->prenom . ' ' . $this->user->nom,
                        'email' => $this->user->email,
                    ]);
    }
}
