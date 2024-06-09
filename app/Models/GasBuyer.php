<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GasBuyer extends Model
{
    use HasFactory;

    protected $table = 'tb_gas_buyer';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'nik',
    ];

    public $timestamps = false;
}
