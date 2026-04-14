<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\UserRegisteredMail;
use Illuminate\Support\Facades\Mail;

class WelcomeNewUser
{
    public function handle(UserRegistered $event): void
    {
        Mail::to($event->user->email)->send(new UserRegisteredMail($event->user));
    }
}
