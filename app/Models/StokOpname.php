<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokOpname extends Model
{
    protected $fillable = [
        'tanggal',
        'keterangan',
        'user_id',
    ];

    public function items()
    {
        return $this->hasMany(StokOpnameItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canBeEdited(): bool
    {
        return $this->created_at->gt(now()->subHours(24));
    } 
}
