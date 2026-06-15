<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserService {

    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function create(array $data): User {
        $role = $data['roles'];
        unset($data['roles']);

        return $this->userRepository->create($data, $role);
    }

    public function update(Model $record, array $data): Model {
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $role = $data['roles'];
        unset($data['roles']);

        return $this->userRepository->update($record, $data, $role);
    }

    public function getById(int $id): User {
        return $this->userRepository->getByID($id) ?? abort(404);
    }
}