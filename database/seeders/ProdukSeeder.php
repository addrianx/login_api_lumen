<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('produk')->insert([
            [
                'nama_produk' => 'Intel Core i5-12400F',
                'deskripsi' => 'Processor 6-core untuk gaming dan produktivitas.',
                'harga' => 2900000,
                'stok' => 15,
                'kategori_id' => 1, // Hardware
                'satuan_id' => 2, // unit
            ],
            [
                'nama_produk' => 'ASUS B660M-A WiFi DDR4',
                'deskripsi' => 'Motherboard LGA 1700 dengan WiFi bawaan.',
                'harga' => 2250000,
                'stok' => 10,
                'kategori_id' => 1, // Hardware
                'satuan_id' => 2,
            ],
            [
                'nama_produk' => 'Kingston 16GB DDR4 3200MHz',
                'deskripsi' => 'RAM gaming 16GB performa tinggi.',
                'harga' => 750000,
                'stok' => 25,
                'kategori_id' => 1,
                'satuan_id' => 1,
            ],
            [
                'nama_produk' => 'Logitech G102 Mouse',
                'deskripsi' => 'Mouse gaming RGB dengan sensor presisi tinggi.',
                'harga' => 265000,
                'stok' => 40,
                'kategori_id' => 2, // Peripheral
                'satuan_id' => 1,
            ],
            [
                'nama_produk' => 'Monitor LG 24MP400 24"',
                'deskripsi' => 'Monitor IPS Full HD 75Hz untuk kerja & multimedia.',
                'harga' => 1650000,
                'stok' => 8,
                'kategori_id' => 2,
                'satuan_id' => 2,
            ],
            [
                'nama_produk' => 'Flashdisk SanDisk 64GB USB 3.0',
                'deskripsi' => 'Penyimpanan portabel cepat dan praktis.',
                'harga' => 85000,
                'stok' => 60,
                'kategori_id' => 3, // Aksesoris
                'satuan_id' => 1,
            ],
            [
                'nama_produk' => 'TP-Link TL-WR840N',
                'deskripsi' => 'Wireless Router 300Mbps, cocok untuk rumahan.',
                'harga' => 190000,
                'stok' => 20,
                'kategori_id' => 4, // Networking
                'satuan_id' => 2,
            ],
            [
                'nama_produk' => 'Windows 11 Home Original',
                'deskripsi' => 'Lisensi original Windows 11 Home OEM.',
                'harga' => 1750000,
                'stok' => 5,
                'kategori_id' => 5, // Software
                'satuan_id' => 1,
            ],
        ]);
    }
}
