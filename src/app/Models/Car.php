<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $table = 'cars';

    protected $fillable = [
        'vin',
        'brand',
        'model',
        'year',
        'price',
        'mileage',
        'is_new',
    ];
}
