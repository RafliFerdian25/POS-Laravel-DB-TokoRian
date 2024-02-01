<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kasir extends Model
{
    use HasFactory;

    protected $table = 't_kasir';

    public function product()
    {
        return $this->belongsTo(Barang::class, 'idBarang', 'idBarang');
    }
}
