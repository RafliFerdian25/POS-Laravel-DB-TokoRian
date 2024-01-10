<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 't_barang';

    // tanpa ada timestamps
    public $timestamps = false;


    protected $casts = [
        'id' => 'string',
    ];

    public function type()
    {
        return $this->belongsTo(Jenis::class, 'jenis', 'ID');
    }

    public function merk()
    {
        return $this->belongsTo(Merk::class);
    }

}
