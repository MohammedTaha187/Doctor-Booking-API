<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Doctor Booking API',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: '<h1>Welcome, '.$this->user->name.'!</h1><p>Thank you for registering with our platform. You can now start booking appointments with top doctors.</p>',
        );
    }
}
