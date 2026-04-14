<?php

namespace App\Services\Api\V1\Appointment;

use App\Models\Appointment;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SlotAvailabilityService
{
    /**
     * Cache TTL: 5 minutes (300 seconds).
     */
    private const CACHE_TTL = 300;

    /**
     * Get available slots for a doctor on a specific date — Redis cached.
     */
    public function getAvailableSlots(string $doctorId, string $date)
    {
        $cacheKey = "slots:{$doctorId}:{$date}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($doctorId, $date) {
            $carbonDate = Carbon::parse($date);
            $dayName = strtolower($carbonDate->format('l'));

            $slots = TimeSlot::where('doctor_id', $doctorId)
                ->where('day_of_week', $dayName)
                ->where('is_available', true)
                ->get();

            $bookedSlotIds = Appointment::where('doctor_id', $doctorId)
                ->where('scheduled_date', $date)
                ->whereIn('status', ['pending', 'confirmed'])
                ->pluck('time_slot_id')
                ->toArray();

            return $slots->filter(fn ($slot) => ! in_array($slot->id, $bookedSlotIds))->values();
        });
    }

    /**
     * Check if a specific slot is available on a given date.
     */
    public function isSlotAvailable(string $slotId, string $date): bool
    {
        $slot = TimeSlot::find($slotId);
        if (! $slot || ! $slot->is_available) {
            return false;
        }

        $exists = Appointment::where('time_slot_id', $slotId)
            ->where('scheduled_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        return ! $exists;
    }

    /**
     * Invalidate the cache for a doctor on a specific date (called after booking/cancelling).
     */
    public function invalidate(string $doctorId, string $date): void
    {
        Cache::forget("slots:{$doctorId}:{$date}");
    }
}
