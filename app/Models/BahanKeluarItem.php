<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanKeluarItem extends Model
{
    protected $fillable = [
        'bahan_keluar_id',
        'bahan_id',
        'jumlah',
    ];

    public function bahanKeluar()
    {
        return $this->belongsTo(BahanKeluar::class);
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class);
    }
}
