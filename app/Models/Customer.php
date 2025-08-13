<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'store_id',  // tambahkan store_id
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
