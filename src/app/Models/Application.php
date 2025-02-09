<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';

    protected $fillable = [
        'car_id',
        'phone',
        'status',     // new, sent, error...
        'crm_sent_at'
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
