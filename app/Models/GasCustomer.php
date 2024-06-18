<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GasCustomer extends Model
{
    use HasFactory;

    protected $table = 't_gas_pelanggan';

    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'pelanggan_id');
    }

    public function gasTransactions()
    {
        return $this->hasMany(GasTransaction::class, 'gas_pelanggan_id');
    }
}
