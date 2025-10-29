<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        // Agrega aquí otras columnas reales si existen (por ejemplo: 'code', 'is_active')
    ];

    protected $casts = [
        // 'is_active' => 'boolean',
    ];
}
