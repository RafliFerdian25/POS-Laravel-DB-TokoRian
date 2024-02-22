<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 't_supplier';

    protected $primaryKey = 'IdSupplier';

    protected $guarded = ['IdSupplier'];

    public $timestamps = false;
}
