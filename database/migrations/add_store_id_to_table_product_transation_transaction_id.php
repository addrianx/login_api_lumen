<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Pastikan tabel stores sudah ada sebelum migrasi ini dijalankan

        // Tambah store_id di produk
        if (Schema::hasTable('produk') && !Schema::hasColumn('produk', 'store_id')) {
            Schema::table('produk', function (Blueprint $table) {
                $table->foreignId('store_id')->nullable()->after('id')->constrained('stores')->onDelete('cascade');
            });
        }

        // Tambah store_id di transactions dan ubah kolom lain
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('transactions', 'store_id')) {
                    $table->foreignId('store_id')->nullable()->after('id')->constrained('stores')->onDelete('cascade');
                }

                // Ubah kolom metode_pembayaran jadi nullable (untuk hold/draft)
                if (Schema::hasColumn('transactions', 'metode_pembayaran')) {
                    $table->string('metode_pembayaran')->nullable()->change();
                }

                // Ubah kolom status jadi enum dengan draft
                if (Schema::hasColumn('transactions', 'status')) {
                    $table->enum('status', ['draft', 'pending', 'completed', 'cancelled'])
                          ->default('draft')
                          ->change();
                }
            });
        }

        // Tambah store_id di transaction_items jika perlu (opsional)
        if (Schema::hasTable('transaction_items') && !Schema::hasColumn('transaction_items', 'store_id')) {
            Schema::table('transaction_items', function (Blueprint $table) {
                $table->foreignId('store_id')->nullable()->after('transaction_id')->constrained('stores')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('produk') && Schema::hasColumn('produk', 'store_id')) {
            Schema::table('produk', function (Blueprint $table) {
                $table->dropConstrainedForeignId('store_id');
            });
        }

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (Schema::hasColumn('transactions', 'store_id')) {
                    $table->dropConstrainedForeignId('store_id');
                }
                // Rollback perubahan kolom
                if (Schema::hasColumn('transactions', 'metode_pembayaran')) {
                    $table->string('metode_pembayaran')->nullable(false)->change();
                }
                if (Schema::hasColumn('transactions', 'status')) {
                    $table->string('status')->default('pending')->change();
                }
            });
        }

        if (Schema::hasTable('transaction_items') && Schema::hasColumn('transaction_items', 'store_id')) {
            Schema::table('transaction_items', function (Blueprint $table) {
                $table->dropConstrainedForeignId('store_id');
            });
        }
    }
};
