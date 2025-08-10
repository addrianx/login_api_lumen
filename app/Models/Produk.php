<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $fillable = [
        'nama_produk', 'deskripsi', 'harga', 'harga_modal',
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

    public function stok_produk()
    {
        return $this->hasMany(StokProduk::class, 'produk_id', 'id');
    }

    // Stok akhir (total_masuk - total_keluar)
    public function getStokAkhirAttribute()
    {
        $masuk = $this->stok_produk()->where('tipe', 'masuk')->sum('jumlah');
        $keluar = $this->stok_produk()->where('tipe', 'keluar')->sum('jumlah');
        return $masuk - $keluar;
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class, 'produk_id');
    }

}
