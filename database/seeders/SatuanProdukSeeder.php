<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('satuan_produk')->insert([
            ['nama_satuan' => 'pcs'],
            ['nama_satuan' => 'unit'],
            ['nama_satuan' => 'pack'],
        ]);
    }
}
