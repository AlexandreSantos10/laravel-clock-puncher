<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    protected $table = 'admin_logs';
    protected $fillable = ['log_id', 'user_id', 'acao', 'dados_antigos', 'dados_novos'];

    // ISTO É O QUE TIRA O N/A:
    protected $casts = [
        'dados_antigos' => 'json', // Ou 'array'
        'dados_novos'   => 'json',
    ];

    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getOwnerNameAttribute()
    {
        $dados = $this->dados_antigos;

        if (is_string($dados)) {
            $dados = json_decode($dados, true);
        }

       if (is_array($dados) && array_key_exists('user_id', $dados)) {
            $user = \App\Models\User::query()->find($dados['user_id']);
            return $user ? $user->name : 'Unknown User';
        }

        

        // Se chegar aqui, é porque o user_id não está mesmo lá!
        return 'N/A';
    }
    public function getOriginalDateAttribute()
    {
        $dados = $this->dados_antigos;

        if (is_string($dados)) {
            $dados = json_decode($dados, true);
        }

        // Vai procurar a chave 'data' dentro do log original
        if (is_array($dados) && isset($dados['data'])) {
            return \Carbon\Carbon::parse($dados['data'])->format('d/m/Y');
        }

        return 'N/A';
    }
}