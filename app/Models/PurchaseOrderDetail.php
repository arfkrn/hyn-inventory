<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'bahan_id',
        'jumlah',
        'jumlah_datang',
        'catatan_gudang',
        'satuan',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class);
    }
}
