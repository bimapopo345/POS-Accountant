<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    use HasFactory;

    protected $table = 'retur';
    protected $primaryKey = 'id_retur';
    protected $guarded = [];

    // Relationship dengan Penjualan
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }

    // Relationship dengan ReturDetail
    public function returDetail()
    {
        return $this->hasMany(ReturDetail::class, 'id_retur', 'id_retur');
    }
}
