<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('retur', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Pastikan menggunakan InnoDB
            $table->id('id_retur');
            $table->unsignedInteger('id_penjualan'); // Ubah dari unsignedBigInteger ke unsignedInteger
            $table->date('tanggal_retur');
            $table->decimal('total_retur', 15, 2)->default(0);
            $table->decimal('nilai_neto', 15, 2)->default(0);
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('id_penjualan')
                  ->references('id_penjualan')
                  ->on('penjualan')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur');
    }
}
