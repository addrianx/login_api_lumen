<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $table = 'stores';

    protected $fillable = ['name', 'address', 'phone'];

    public function stokProduk(): HasMany
    {
        return $this->hasMany(StokProduk::class, 'store_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'store_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'store_id');
    }

    public function consumers(): HasMany
    {
        return $this->hasMany(Consumer::class, 'store_id');
    }
}