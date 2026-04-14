<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\Interfaces\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function create(array $data)
    {
        return Payment::create($data);
    }

    public function update(string $id, array $data)
    {
        $record = Payment::find($id);
        if ($record) {
            $record->update($data);

            return $record;
        }

        return null;
    }

    public function delete(string $id)
    {
        $record = Payment::find($id);
        if ($record) {
            return $record->delete();
        }

        return false;
    }

    public function find(string $id)
    {
        return Payment::find($id);
    }

    public function all()
    {
        return Payment::all();
    }
}
