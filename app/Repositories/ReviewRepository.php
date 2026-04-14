<?php

namespace App\Repositories;

use App\Models\Review;
use App\Repositories\Interfaces\ReviewRepositoryInterface;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function create(array $data)
    {
        return Review::create($data);
    }

    public function update(string $id, array $data)
    {
        $record = Review::with([
            'patient',
            'doctor.user',
            'doctor.specialty',
            'doctor.translations',
            'doctor.specialty.translations',
            'appointment',
        ])->find($id);
        if ($record) {
            $record->update($data);

            return $record;
        }

        return null;
    }

    public function delete(string $id)
    {
        $record = Review::find($id);
        if ($record) {
            return $record->delete();
        }

        return false;
    }

    public function find(string $id)
    {
        return Review::with([
            'patient',
            'doctor.user',
            'doctor.specialty',
            'doctor.translations',
            'doctor.specialty.translations',
            'appointment',
        ])->find($id);
    }

    public function all()
    {
        return Review::with([
            'patient',
            'doctor.user',
            'doctor.specialty',
            'doctor.translations',
            'doctor.specialty.translations',
            'appointment',
        ])->get();
    }
}
