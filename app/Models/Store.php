<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    // Nama tabel jika tidak menggunakan konvensi plural
    protected $table = 'stores';

    // Mass assignable fields
    protected $fillable = [
        'name',
        'address',
        'phone',
        // Tambah kolom lain yang kamu perlukan
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Relasi satu store punya banyak produk
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'store_id');
    }

    /**
     * Relasi satu store punya banyak transaksi
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'store_id');
    }
}
