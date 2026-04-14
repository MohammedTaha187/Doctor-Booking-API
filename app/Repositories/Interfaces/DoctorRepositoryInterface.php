<?php

namespace App\Repositories\Interfaces;

interface DoctorRepositoryInterface
{
    public function create(array $data);

    public function update(string $id, array $data);

    public function delete(string $id);

    public function find(string $id);

    public function all();

    public function findByUserId(string $userId);

    public function search(array $filters);
}
