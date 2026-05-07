<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class logs extends Model
{
 
 public function User(){
   
    return $this->belongsTo(User::class);
}

    public $is_clock_out = false;
    public $autor_personalizado = null; 
    public $acao_personalizada = null;  
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
