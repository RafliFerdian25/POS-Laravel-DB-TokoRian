<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gas extends Model
{
    use HasFactory;

    protected $table = 'tb_gas';

    protected $fillable = [
        'date',
        'stok',
    ];

    public $timestamps = false;
}
