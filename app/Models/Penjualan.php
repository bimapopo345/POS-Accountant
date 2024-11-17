<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan';
    protected $guarded = [];

    // Relationship dengan Member
    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id_member');
    }

    // Relationship dengan User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    // Relationship dengan PenjualanDetail
    public function penjualanDetail()
    {
        return $this->hasMany(PenjualanDetail::class, 'id_penjualan', 'id_penjualan');
    }
}
