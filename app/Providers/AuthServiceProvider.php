<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Locale;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Specialty;
use App\Models\TimeSlot;
use App\Models\Translation;
use App\Policies\Api\V1\AppointmentPolicy;
use App\Policies\Api\V1\DoctorPolicy;
use App\Policies\Api\V1\ReviewPolicy;
use App\Policies\LocalePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\SettingPolicy;
use App\Policies\SpecialtyPolicy;
use App\Policies\TimeSlotPolicy;
use App\Policies\TranslationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Appointment::class => AppointmentPolicy::class,
        Doctor::class => DoctorPolicy::class,
        Locale::class => LocalePolicy::class,
        Payment::class => PaymentPolicy::class,
        Review::class => ReviewPolicy::class,
        Setting::class => SettingPolicy::class,
        Specialty::class => SpecialtyPolicy::class,
        TimeSlot::class => TimeSlotPolicy::class,
        Translation::class => TranslationPolicy::class,
    ];
}
