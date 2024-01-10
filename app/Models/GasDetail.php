<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GasDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'gas_id',
        'user_id',
        'name',
        'qty',
        'amount',
        'take',
    ];

    public function gas()
    {
        return $this->belongsTo(Gas::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}