<?php

use App\Jobs\SendAppointmentReminder;
use App\Models\Appointment;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Dispatch reminders for appointments happening in the next 24-25 hours
Schedule::call(function () {
    Appointment::whereBetween('scheduled_date', [
        now()->addHours(24)->toDateString(),
        now()->addHours(25)->toDateString(),
    ])
        ->whereIn('status', ['pending', 'confirmed'])
        ->each(fn (Appointment $appointment) => SendAppointmentReminder::dispatch($appointment));
})->hourly()->name('dispatch-appointment-reminders');
