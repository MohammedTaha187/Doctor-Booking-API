<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public string $recipientType
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Appointment Reminder — Tomorrow',
        );
    }

    public function content(): Content
    {
        $date = $this->appointment->scheduled_date;
        $time = $this->appointment->scheduled_time;

        $message = $this->recipientType === 'doctor'
            ? "Reminder: You have an appointment tomorrow on {$date} at {$time}."
            : "Reminder: Your appointment is tomorrow on {$date} at {$time}. Please be on time.";

        return new Content(
            htmlString: "<h1>Appointment Reminder</h1><p>{$message}</p>",
        );
    }
}
