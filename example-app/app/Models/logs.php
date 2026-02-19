<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class logs extends Model
{
    protected $table = 'logs';

    protected $fillable = [
        'id',
        'user_id',
        'data',
        'entrada',
        'final_almoço',
        'saida',
        'total_horas',
        'obs',
        'created_by',
        'updated_by'
    ];
}
