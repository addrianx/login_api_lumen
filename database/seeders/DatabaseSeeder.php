<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SatuanProdukSeeder::class, // Seeder untuk satuan
            KategoriProdukSeeder::class, // Seeder untuk kategori
            UsersTableSeeder::class,
            ProdukSeeder::class, // Baru kemudian produk
            StokProdukSeeder::class
        ]); 
    }
}