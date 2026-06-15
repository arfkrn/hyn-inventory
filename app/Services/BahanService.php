<?php

namespace App\Services;

use App\Models\Bahan;
use App\Repositories\Contracts\BahanRepositoryInterface;

class BahanService {
    public function __construct(
        protected BahanRepositoryInterface $bahanRepository
    ) {}

    public function create(array $data): Bahan {
        return $this->bahanRepository->create($data);
    }

    public function getById(int $id): Bahan {
        return $this->getById($id) ?? abort(404);
    }
}