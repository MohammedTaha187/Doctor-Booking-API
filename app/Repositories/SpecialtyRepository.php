<?php

namespace App\Repositories;

use App\Models\Specialty;
use App\Repositories\Interfaces\SpecialtyRepositoryInterface;

class SpecialtyRepository implements SpecialtyRepositoryInterface
{
    public function create(array $data)
    {
        return Specialty::create($data);
    }

    public function update(string $id, array $data)
    {
        $record = Specialty::find($id);
        if ($record) {
            $record->update($data);

            return $record;
        }

        return null;
    }

    public function delete(string $id)
    {
        $record = Specialty::find($id);
        if ($record) {
            return $record->delete();
        }

        return false;
    }

    public function find(string $id)
    {
        return Specialty::find($id);
    }

    public function all()
    {
        return Specialty::all();
    }
}
