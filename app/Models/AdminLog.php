<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    protected $table = 'admin_logs';
   protected $fillable = ['log_id', 'user_id', 'admin_id', 'acao', 'dados_antigos', 'dados_novos']; 

   

    protected $casts = [
        'dados_antigos' => 'json', 
        'dados_novos'   => 'json',
    ];
public function decisor()
{
    return $this->belongsTo(User::class, 'admin_id');
}
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

        return 'N/A';
    }
    public function getOriginalDateAttribute()
    {
        $dados = $this->dados_antigos;

        if (is_string($dados)) {
            $dados = json_decode($dados, true);
        }

        if (is_array($dados) && isset($dados['data'])) {
            return \Carbon\Carbon::parse($dados['data'])->format('d/m/Y');
        }

        return 'N/A';
    }
}