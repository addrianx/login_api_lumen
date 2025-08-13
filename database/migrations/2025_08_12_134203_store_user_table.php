<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('store_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('role'); // admin, manager, cashier
            $table->timestamps();

            $table->unique(['store_id', 'user_id']); // user tidak ganda di toko yang sama
        });
    }

    public function down()
    {
        Schema::dropIfExists('store_user');
    }
};
