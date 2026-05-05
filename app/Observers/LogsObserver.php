<?php
namespace App\Observers;

use App\Models\logs;
use App\Models\AdminLog;
use Illuminate\Support\Facades\Auth;

class LogsObserver
{
    
    public function updated(logs $logs)
    {
        // Se a variável tiver 'EXIT', usa isso. Se estiver vazia, foi edição normal (EDIT).
        $acao = $logs->tipo_acao_custom ?? 'EDIT';

        \App\Models\AdminLog::create([
            'log_id'       => $logs->id,
            'user_id'      => \Illuminate\Support\Facades\Auth::id() ?? 1,
            'acao'         => $acao,
            'dados_antigos' => $logs->getOriginal(), 
            'dados_novos'   => $logs->getAttributes(),
        ]);
    }

    public function deleted(logs $logs)
    {
        AdminLog::create([
            'log_id'       => $logs->id,
            'user_id'      => Auth::id() ?? 1,
            'acao'         => 'DELETE',
            'dados_antigos' => $logs->getOriginal(), 
            'dados_novos'   => null,
        ]);
    }
}