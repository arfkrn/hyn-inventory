<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanMasukItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bahan_masuk_id',
        'bahan_id',
        'jumlah',
    ];

    public function bahanMasuk()
    {
        return $this->belongsTo(BahanMasuk::class);
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class);
    }
}
