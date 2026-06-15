<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

interface UserRepositoryInterface {
    public function getByID(int $id): ?User;
    public function create(array $data, int $role): User;
    public function update(Model $record, array $data, int $role): Model;
}