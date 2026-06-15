<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanMasuk extends Model
{
    use HasFactory;
    protected $table = 'bahan_masuk';

    protected $fillable = [
        'tanggal',
        'nama_supplier',
        'keterangan',
        'user_id',
        'purchase_order_id',
    ];

    public function items()
    {
        return $this->hasMany(BahanMasukItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function canBeEdited(): bool
    {
        return $this->created_at->gt(now()->subHours(24));
    } 
}
