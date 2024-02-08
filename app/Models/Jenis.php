<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    use HasFactory;

    protected $table = 'p_jenis';

    protected $primaryKey = 'ID';

    protected $casts = [
        'ID' => 'string',
    ];

    protected $guarded = [];

    // tanpa ada timestamps
    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(Product::class, 'jenis', 'ID');
    }
}
