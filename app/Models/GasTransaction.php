<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GasTransaction extends Model
{
    use HasFactory;

    protected $table = 't_transaksi_gas';

    protected $guarded = ['id'];

    public function gasCustomer()
    {
        return $this->belongsTo(GasCustomer::class, 'gas_pelanggan_id', 'id');
    }
}
