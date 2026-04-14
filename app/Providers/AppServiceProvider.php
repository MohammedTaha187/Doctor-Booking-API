<?php

namespace App\Providers;

use App\Events\AppointmentBooked;
use App\Events\AppointmentCancelled;
use App\Events\UserRegistered;
use App\Listeners\NotifyAppointmentParticipants;
use App\Listeners\WelcomeNewUser;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            UserRegistered::class,
            [WelcomeNewUser::class, 'handle']
        );

        Event::listen(
            AppointmentBooked::class,
            [NotifyAppointmentParticipants::class, 'handleAppointmentBooked']
        );

        Event::listen(
            AppointmentCancelled::class,
            [NotifyAppointmentParticipants::class, 'handleAppointmentCancelled']
        );
    }
}
