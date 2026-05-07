<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogApproval extends Model
{
    
    protected $fillable = ['log_id', 'user_id', 'dados_novos', 'status'];

    
    protected $casts = [
        'dados_novos' => 'array'
    ];

    public function log()
    {
        return $this->belongsTo(logs::class, 'log_id');
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}