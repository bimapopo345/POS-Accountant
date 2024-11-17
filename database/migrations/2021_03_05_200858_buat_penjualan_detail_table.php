<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BuatPenjualanDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->increments('id_penjualan_detail');
            $table->unsignedInteger('id_penjualan'); // Ubah dari integer ke unsignedInteger
            $table->unsignedInteger('id_produk'); // Pastikan juga unsignedInteger
            $table->integer('harga_jual');
            $table->integer('jumlah');
            $table->tinyInteger('diskon')->default(0);
            $table->integer('subtotal');
            $table->timestamps();

            // Optional: Tambahkan foreign key constraints jika diperlukan
            // $table->foreign('id_penjualan')->references('id_penjualan')->on('penjualan')->onDelete('cascade');
            // $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penjualan_detail');
    }
}
