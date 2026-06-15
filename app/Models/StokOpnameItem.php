<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokOpnameItem extends Model
{
    protected $fillable = [
        'stok_opname_id',
        'bahan_id',
        'stok_sistem',
        'stok_fisik',
        'selisih'
    ];

    public function stokOpname()
    {
        return $this->belongsTo(StokOpname::class);
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class);
    }

    protected static function booted()
    {
        static::saved(function ($stokOpnameItem) {
            $stokOpnameItem->bahan->update(['stok' => $stokOpnameItem->stok_fisik]);
        });
    }
}
