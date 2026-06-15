<?php

namespace App\Repositories\Contracts;

use App\Models\Bahan;

interface BahanRepositoryInterface {
    public function create(array $data): Bahan;
    public function getById(int $id): ?Bahan;
    public function updateStok(string $ids, string $cases): void;
}