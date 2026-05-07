<?php
namespace App\Observers;

use App\Models\logs;
use App\Models\AdminLog;
use Illuminate\Support\Facades\Auth;

class LogsObserver
{
    
    public function updated(logs $logs)
    {
        $autorId = $logs->autor_personalizado ?? \Illuminate\Support\Facades\Auth::id() ?? 1;
        
        $acao = $logs->acao_personalizada ?? ($logs->is_clock_out ? 'EXIT' : 'EDIT');

        \App\Models\AdminLog::create([
            'log_id'       => $logs->id,
            'user_id'      => $autorId,
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