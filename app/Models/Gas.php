<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gas extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'quota',
    ];
    
    public function gas_details()
    {
        return $this->hasMany(GasDetail::class);
    }
}