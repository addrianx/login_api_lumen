<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 12, 2)->default(0);

            // Relasi
            $table->unsignedBigInteger('kategori_id');
            $table->unsignedBigInteger('satuan_id');

            // Foreign key
            $table->foreign('kategori_id')->references('id')->on('kategori_produk')->onDelete('cascade');
            $table->foreign('satuan_id')->references('id')->on('satuan_produk')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
