<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('kategori_produk')->insert([
            ['nama_kategori' => 'Hardware'],
            ['nama_kategori' => 'Peripheral'],
            ['nama_kategori' => 'Aksesoris'],
            ['nama_kategori' => 'Networking'],
            ['nama_kategori' => 'Software'],
        ]);
    }
}
