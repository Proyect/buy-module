<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'tax_id',
        'contact_name',
        'contact_email',
        'contact_phone',
        'address',
        'payment_terms',
        'currency',
        'rating',
        'status',
    ];

    protected $casts = [
        'address' => 'array',
        'rating' => 'decimal:1',
    ];
}
