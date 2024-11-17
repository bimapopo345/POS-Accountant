<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturDetail extends Model
{
    use HasFactory;

    protected $table = 'retur_detail';
    protected $primaryKey = 'id_retur_detail';
    protected $guarded = [];

    // Relationship dengan Retur
    public function retur()
    {
        return $this->belongsTo(Retur::class, 'id_retur', 'id_retur');
    }

    // Relationship dengan Produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}
