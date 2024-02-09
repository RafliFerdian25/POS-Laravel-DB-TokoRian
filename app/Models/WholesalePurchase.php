<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WholesalePurchase extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 't_belanja';

    public function product()
    {
        return $this->belongsTo(Product::class, 'idBarang');
    }
}
