<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmFailure extends Model
{
    use HasFactory;

    protected $table = 'crm_failures';

    protected $fillable = [
        'application_id',
        'attempts',
        'last_attempt_at',
        'error_message',
        'resolved',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
