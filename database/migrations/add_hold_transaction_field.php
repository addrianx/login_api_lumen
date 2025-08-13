<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Tambah store_id kalau multi-toko
            if (!Schema::hasColumn('transactions', 'store_id')) {
                $table->foreignId('store_id')->nullable()->after('id')
                      ->constrained('stores')->onDelete('set null');
            }

            // Ubah metode_pembayaran jadi nullable
            if (Schema::hasColumn('transactions', 'metode_pembayaran')) {
                $table->string('metode_pembayaran')->nullable()->change();
            }

            // Ubah status jadi enum yang ada draft/hold
            if (Schema::hasColumn('transactions', 'status')) {
                $table->enum('status', ['draft', 'pending', 'completed', 'cancelled'])
                      ->default('draft')
                      ->change();
            }

            // Tambah kolom hold_until kalau belum ada
            if (!Schema::hasColumn('transactions', 'hold_until')) {
                $table->timestamp('hold_until')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Rollback kolom tambahan
            if (Schema::hasColumn('transactions', 'store_id')) {
                $table->dropConstrainedForeignId('store_id');
            }
            if (Schema::hasColumn('transactions', 'hold_until')) {
                $table->dropColumn('hold_until');
            }

            // Kembalikan status ke string biasa
            if (Schema::hasColumn('transactions', 'status')) {
                $table->string('status')->default('pending')->change();
            }

            // Kembalikan metode_pembayaran ke not null
            if (Schema::hasColumn('transactions', 'metode_pembayaran')) {
                $table->string('metode_pembayaran')->nullable(false)->change();
            }
        });
    }
};
