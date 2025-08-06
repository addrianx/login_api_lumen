<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StokProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $now = now();

        DB::table('stok_produk')->insert([
            ['produk_id' => 1, 'tipe' => 'masuk', 'jumlah' => 15, 'keterangan' => 'Stok awal', 'created_at' => $now, 'updated_at' => $now],
            ['produk_id' => 2, 'tipe' => 'masuk', 'jumlah' => 10, 'keterangan' => 'Stok awal', 'created_at' => $now, 'updated_at' => $now],
            ['produk_id' => 3, 'tipe' => 'masuk', 'jumlah' => 25, 'keterangan' => 'Stok awal', 'created_at' => $now, 'updated_at' => $now],
            ['produk_id' => 4, 'tipe' => 'masuk', 'jumlah' => 40, 'keterangan' => 'Stok awal', 'created_at' => $now, 'updated_at' => $now],
            ['produk_id' => 5, 'tipe' => 'masuk', 'jumlah' => 8,  'keterangan' => 'Stok awal', 'created_at' => $now, 'updated_at' => $now],
            ['produk_id' => 6, 'tipe' => 'masuk', 'jumlah' => 60, 'keterangan' => 'Stok awal', 'created_at' => $now, 'updated_at' => $now],
            ['produk_id' => 7, 'tipe' => 'masuk', 'jumlah' => 20, 'keterangan' => 'Stok awal', 'created_at' => $now, 'updated_at' => $now],
            ['produk_id' => 8, 'tipe' => 'masuk', 'jumlah' => 5,  'keterangan' => 'Stok awal', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
