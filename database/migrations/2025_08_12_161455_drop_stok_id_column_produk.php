<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('produk', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['store_id']); // sesuaikan dengan nama kolom relasi
            
            // Baru drop kolom
            $table->dropColumn('store_id');
        });
    }

    public function down()
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });
    }
};