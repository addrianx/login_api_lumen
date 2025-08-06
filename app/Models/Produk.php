<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $fillable = [
        'nama_produk', 'deskripsi', 'harga', 'stok', 
        'kategori_id', 'satuan_id'
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_id');
    }

    public function satuan()
    {
        return $this->belongsTo(SatuanProduk::class, 'satuan_id');
    }

    public function stokHistori()
    {
        return $this->hasMany(StokProduk::class, 'produk_id');
    }
}
