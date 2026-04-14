<?php

namespace App\Repositories\Interfaces;

interface PaymentRepositoryInterface
{
    public function create(array $data);

    public function update(string $id, array $data);

    public function delete(string $id);

    public function find(string $id);

    public function all();
}
