<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'customer_id', 
        'store_id',       // tambah store_id
        'subtotal', 
        'diskon', 
        'total', 
        'metode_pembayaran', 
        'status', 
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
