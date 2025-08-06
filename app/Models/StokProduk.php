<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokProduk extends Model
{
    protected $table = 'stok_produk';
    protected $fillable = ['produk_id', 'tipe', 'jumlah', 'keterangan'];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
