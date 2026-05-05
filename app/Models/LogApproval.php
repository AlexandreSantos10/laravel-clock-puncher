<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogApproval extends Model
{
    // Permite guardar dados nestas colunas
    protected $fillable = ['log_id', 'user_id', 'dados_novos', 'status'];

    // Diz ao Laravel para converter o JSON automaticamente
    protected $casts = [
        'dados_novos' => 'array'
    ];

    // Relação com o Log original
    public function log()
    {
        return $this->belongsTo(logs::class, 'log_id');
    }

    // Relação com o User que fez o pedido
    public function solicitante()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}