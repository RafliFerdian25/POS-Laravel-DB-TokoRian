<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barcode extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $primaryKey = 'ID';

    // tanpa ada timestamps
    public $timestamps = false;

    protected $table = 't_barcode';

    public function product()
    {
        return $this->belongsTo(Product::class, 'idBarang', 'idBarang');
    }
}
