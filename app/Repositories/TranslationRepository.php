<?php

namespace App\Repositories;

use App\Models\Translation;
use App\Repositories\Interfaces\TranslationRepositoryInterface;

class TranslationRepository implements TranslationRepositoryInterface
{
    public function create(array $data)
    {
        return Translation::create($data);
    }

    public function update(string $id, array $data)
    {
        $record = Translation::find($id);
        if ($record) {
            $record->update($data);

            return $record;
        }

        return null;
    }

    public function delete(string $id)
    {
        $record = Translation::find($id);
        if ($record) {
            return $record->delete();
        }

        return false;
    }

    public function find(string $id)
    {
        return Translation::find($id);
    }

    public function all()
    {
        return Translation::all();
    }
}
