<?php 

namespace App\Repositories;

use App\Models\Bahan;
use App\Repositories\Contracts\BahanRepositoryInterface;
use DB;

class BahanRepository implements BahanRepositoryInterface {
    public function create(array $data): Bahan {
        return Bahan::create($data);
    }

    public function getById(int $id): ?Bahan {
        return Bahan::find($id);
    }

    public function updateStok(string $ids, string $cases): void {
        DB::statement("UPDATE bahans SET stok = (CASE id {$cases} ELSE stok END) WHERE id IN ($ids)");
    }
}