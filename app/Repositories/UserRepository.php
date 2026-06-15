<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class UserRepository implements UserRepositoryInterface {
    public function create(array $data, int $role): User {
        $user = User::create($data);
        $user->assignRole($role);

        return $user;
    }

    public function getByID(int $id): ?User {
        return User::find($id);
    }

    public function update(Model $record, array $data, int $role): Model {
        $record->update($data);
        $record->syncRoles($role);

        return $record;
    }
}