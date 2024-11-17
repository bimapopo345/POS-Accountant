<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturDetailTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('retur_detail', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Pastikan menggunakan InnoDB
            $table->increments('id_retur_detail'); // Primary key: unsigned integer
            $table->unsignedBigInteger('id_retur'); // Sesuaikan dengan tipe data di tabel 'retur'
            $table->unsignedInteger('id_produk'); // Sesuaikan dengan tipe data di tabel 'produk'
            $table->integer('jumlah_retur');
            $table->decimal('harga_retur', 15, 2);
            $table->text('alasan_retur');
            $table->decimal('subtotal_retur', 15, 2);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_retur')
                  ->references('id_retur')
                  ->on('retur')
                  ->onDelete('cascade');

            $table->foreign('id_produk')
                  ->references('id_produk')
                  ->on('produk')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retur_detail', function (Blueprint $table) {
            $table->dropForeign(['id_retur']);
            $table->dropForeign(['id_produk']);
        });
        Schema::dropIfExists('retur_detail');
    }
}
