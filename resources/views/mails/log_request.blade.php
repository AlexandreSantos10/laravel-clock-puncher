<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px;">
    <h2 style="color: #1f2937;">Novo Pedido de Alteração de Ponto</h2>
    
    <p>O utilizador <strong>{{ $solicitante->name }}</strong> pediu para alterar as suas horas do dia <strong>{{ \Carbon\Carbon::parse($logOriginal->data)->format('d/m/Y') }}</strong>.</p>

    <div style="background-color: #f3f4f6; padding: 15px; border-radius: 6px; margin: 20px 0;">
        <h4 style="margin-top: 0; color: #4b5563;">Novos Horários Solicitados:</h4>
        <ul style="list-style-type: none; padding: 0; margin: 0; color: #1f2937;">
            <li style="margin-bottom: 8px;"><strong>Entrada:</strong> {{ $novosDados['entrada'] }}</li>
            <li style="margin-bottom: 8px;"><strong>Saída:</strong> {{ $novosDados['saida'] }}</li>
            <li style="margin-bottom: 8px;"><strong>Total Horas:</strong> {{ $novosDados['total_horas'] }}</li>
            <li><strong>Observações:</strong> {{ $novosDados['obs'] }}</li>
        </ul>
    </div>

    <p style="margin-top: 30px;">
        <a href="{{ route('admin.approve_log', $approvalId) }}" style="background-color: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold; margin-right: 10px;">Aprovar Alteração</a>
        
        <a href="{{ route('admin.reject_log', $approvalId) }}" style="background-color: #ef4444; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">Rejeitar</a>
    </p>
    
    <p style="font-size: 12px; color: #9ca3af; margin-top: 40px;">Este e-mail foi gerado pelo Sistema de Timely.</p>
</div>