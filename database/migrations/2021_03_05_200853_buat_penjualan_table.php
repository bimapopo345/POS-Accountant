<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BuatPenjualanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::create('penjualan', function (Blueprint $table) {
            $table->increments('id_penjualan'); // Ini adalah unsigned integer dan primary key
            $table->unsignedInteger('id_member');
            $table->unsignedInteger('id_user');
            $table->integer('total_item');
            $table->integer('total_harga');
            $table->tinyInteger('diskon')->default(0);
            $table->integer('bayar')->default(0);
            $table->integer('diterima')->default(0);
            $table->timestamps();

            // Optional: Tambahkan foreign key constraints jika diperlukan
            // $table->foreign('id_member')->references('id_member')->on('member')->onDelete('set null');
            // $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penjualan');
    }
}
