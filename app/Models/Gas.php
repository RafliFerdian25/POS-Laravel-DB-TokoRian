<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gas extends Model
{
    use HasFactory;

    protected $table = 't_gas';

    protected $guarded = ['id'];

    public function gasCustomers()
    {
        return $this->hasMany(GasCustomer::class, 'gas_id');
    }

    public function manyGasCustomers()
    {
        return $this->belongsToMany(Customer::class, 't_gas_pelanggan', 'gas_id', 'pelanggan_id')->withPivot('kuota');
    }
}
