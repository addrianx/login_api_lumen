<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokProduk extends Model
{
    protected $table = 'stok_produk';

    protected $fillable = [
        'produk_id', 
        'store_id',     // tambahkan store_id supaya bisa mass assign
        'tipe', 
        'jumlah', 
        'keterangan'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
