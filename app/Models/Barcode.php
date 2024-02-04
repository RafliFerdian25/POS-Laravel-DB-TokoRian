<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barcode extends Model
{
    use HasFactory;

    protected $guarded = [];

    // tanpa ada timestamps
    public $timestamps = false;

    protected $primaryKey = 'idBarang';

    protected $casts = [
        'id' => 'string',
    ];

    protected $table = 't_barcode';

    public function product()
    {
        return $this->belongsTo(Barang::class, 'idBarang', 'idBarang');
    }
}
