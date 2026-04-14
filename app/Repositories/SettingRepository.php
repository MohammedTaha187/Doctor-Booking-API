<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\Interfaces\SettingRepositoryInterface;

class SettingRepository implements SettingRepositoryInterface
{
    public function create(array $data)
    {
        return Setting::create($data);
    }

    public function update(string $id, array $data)
    {
        $record = Setting::find($id);
        if ($record) {
            $record->update($data);

            return $record;
        }

        return null;
    }

    public function delete(string $id)
    {
        $record = Setting::find($id);
        if ($record) {
            return $record->delete();
        }

        return false;
    }

    public function find(string $id)
    {
        return Setting::find($id);
    }

    public function all()
    {
        return Setting::all();
    }
}
