<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GasTransaction extends Model
{
    use HasFactory;

    protected $table = 'tb_gas_transaction';

    protected $fillable = [
        'gas_id',
        'gas_buyer_id',
        'pay',
        'empty_gas',
        'take',
        'quota',
        'created_at',
    ];

    const UPDATED_AT = null;
}
