<?php

namespace App\Repositories;

use App\Models\Doctor;
use App\Repositories\Interfaces\DoctorRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class DoctorRepository implements DoctorRepositoryInterface
{
    public function create(array $data)
    {
        return Doctor::create($data);
    }

    public function update(string $id, array $data)
    {
        $record = Doctor::find($id);
        if ($record) {
            $record->update($data);

            return $record;
        }

        return null;
    }

    public function delete(string $id)
    {
        $record = Doctor::find($id);
        if ($record) {
            return $record->delete();
        }

        return false;
    }

    public function find(string $id)
    {
        return Doctor::find($id);
    }

    public function all()
    {
        return Doctor::all();
    }

    public function findByUserId(string $userId)
    {
        return Doctor::where('user_id', $userId)->first();
    }

    public function search(array $filters)
    {
        $query = Doctor::query()->with(['user', 'specialty']);

        // Filter by Specialty
        if (! empty($filters['specialty_id'])) {
            $query->where('specialty_id', $filters['specialty_id']);
        }

        // Filter by Name (Search in User model)
        if (! empty($filters['name'])) {
            $query->whereHas('user', function (Builder $q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['name'].'%');
            });
        }

        // Filter by Price Range
        if (! empty($filters['min_fee'])) {
            $query->where('consultation_fee', '>=', $filters['min_fee']);
        }
        if (! empty($filters['max_fee'])) {
            $query->where('consultation_fee', '<=', $filters['max_fee']);
        }

        // Sort by Rating (Default)
        $query->orderBy('rating', 'desc');

        return $query->paginate($filters['per_page'] ?? 15);
    }
}
