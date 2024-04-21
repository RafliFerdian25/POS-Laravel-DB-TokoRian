<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class BoxOpen extends Model
{
    use HasFactory;

    protected $table = 't_buka_kardus';

    protected $guarded = [];

    public function productBox(): Relation
    {
        return $this->belongsTo(Product::class, 'dus_id', 'idBarang');
    }

    public function productRetail(): Relation
    {
        return $this->belongsTo(Product::class, 'retail_id', 'idBarang');
    }
}