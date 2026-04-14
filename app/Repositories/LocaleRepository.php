<?php

namespace App\Repositories;

use App\Models\Locale;
use App\Repositories\Interfaces\LocaleRepositoryInterface;

class LocaleRepository implements LocaleRepositoryInterface
{
    public function create(array $data)
    {
        return Locale::create($data);
    }

    public function update(string $id, array $data)
    {
        $record = Locale::find($id);
        if ($record) {
            $record->update($data);

            return $record;
        }

        return null;
    }

    public function delete(string $id)
    {
        $record = Locale::find($id);
        if ($record) {
            return $record->delete();
        }

        return false;
    }

    public function find(string $id)
    {
        return Locale::find($id);
    }

    public function all()
    {
        return Locale::all();
    }
}
