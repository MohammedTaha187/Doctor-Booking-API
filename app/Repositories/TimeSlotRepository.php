<?php

namespace App\Repositories;

use App\Models\TimeSlot;
use App\Repositories\Interfaces\TimeSlotRepositoryInterface;

class TimeSlotRepository implements TimeSlotRepositoryInterface
{
    public function create(array $data)
    {
        return TimeSlot::create($data);
    }

    public function update(string $id, array $data)
    {
        $record = TimeSlot::find($id);
        if ($record) {
            $record->update($data);

            return $record;
        }

        return null;
    }

    public function delete(string $id)
    {
        $record = TimeSlot::find($id);
        if ($record) {
            return $record->delete();
        }

        return false;
    }

    public function find(string $id)
    {
        return TimeSlot::find($id);
    }

    public function all()
    {
        return TimeSlot::all();
    }

    public function getByDoctorId(string $doctorId)
    {
        return TimeSlot::where('doctor_id', $doctorId)->get();
    }
}
