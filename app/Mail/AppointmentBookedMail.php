<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentBookedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment, public string $recipientType) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Appointment Booked',
        );
    }

    public function content(): Content
    {
        $date = $this->appointment->scheduled_date;
        $time = $this->appointment->scheduled_time;
        $message = $this->recipientType === 'doctor'
            ? "A new appointment has been booked with you for $date at $time."
            : "Your appointment has been successfully booked for $date at $time.";

        return new Content(
            htmlString: "<h1>Appointment Confirmation</h1><p>$message</p><p>Status: Pending Confirmation</p>",
        );
    }
}
