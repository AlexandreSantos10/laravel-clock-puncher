<?php

namespace App\Observers;

use App\Models\logs;
use App\Models\AdminLog;
use Illuminate\Support\Facades\Auth;

class LogsObserver
{
    public function updated(logs $logs)
    {
        $acao = $logs->acao_personalizada ?? $logs->tipo_acao_custom ?? ($logs->is_clock_out ? 'EXIT' : 'EDIT');

       if ($acao === 'EXIT' || $acao === 'APPROVED') {
            return;
        }

        $autorId = $logs->autor_personalizado ?? Auth::id() ?? 1;

        AdminLog::create([
            'log_id'        => $logs->id,
            'user_id'       => $autorId,
            'acao'          => $acao,
            'dados_antigos' => $logs->getOriginal(), 
            'dados_novos'   => $logs->getAttributes(),
        ]);
    }

    public function deleted(logs $logs)
    {
        // Eliminações são sempre registadas
        AdminLog::create([
            'log_id'        => $logs->id,
            'user_id'       => Auth::id() ?? 1,
            'acao'          => 'DELETE',
            'dados_antigos' => $logs->getOriginal(), 
            'dados_novos'   => null,
        ]);
    }
}