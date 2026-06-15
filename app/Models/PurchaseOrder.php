<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
   protected $table = "purchase_order";
    
    protected $fillable = [
        'user_id',
        'no_po',
        'tanggal_po',
        'status',
        'keterangan',
        'nama_supplier',
    ];

    const STATUS_PROSES = 'proses';
    const STATUS_BELUM_LENGKAP = 'belum_lengkap';
    const STATUS_SELESAI = 'selesai';

    public function items()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bahanMasuks()
    {
        return $this->hasMany(BahanMasuk::class);
    }
}
