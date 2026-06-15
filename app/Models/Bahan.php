<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bahan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_bahan',
        'satuan',
        'min_stok',
        'stok'
    ];

    public function bahanMasukItems()
    {
        return $this->hasMany(BahanMasukItem::class);
    }
    public function bahanKeluarItems()
    {
        return $this->hasMany(BahanKeluarItem::class);
    }

    public function stokOpnameItems()
    {
        return $this->hasMany(StokOpnameItem::class);
    }

    public function purchaseOrderDetail()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
