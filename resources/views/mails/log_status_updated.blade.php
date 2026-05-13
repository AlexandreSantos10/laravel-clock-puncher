<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px;">
    <h2 style="color: {{ $status === 'approved' ? '#10b981' : '#ef4444' }};">
        Pedido de Alteração {{ $status === 'approved' ? 'Aceite' : 'Recusado' }}
    </h2>
    
    <p>Olá <strong>{{ $user->name }}</strong>,</p>
    <p>O teu pedido de alteração para o dia <strong>{{ \Carbon\Carbon::parse($logOriginal->data)->format('d/m/Y') }}</strong> foi processado pelo administrador.</p>

    <div style="background-color: #f3f4f6; padding: 15px; border-radius: 6px; margin: 20px 0;">
        <h4 style="margin-top: 0; color: #4b5563;">Detalhes do Pedido:</h4>
        <ul style="list-style-type: none; padding: 0; margin: 0; color: #1f2937;">
            <li style="margin-bottom: 8px;"><strong>Status Final:</strong> 
                <span style="color: {{ $status === 'approved' ? '#10b981' : '#ef4444' }}; font-weight: bold;">
                    {{ strtoupper($status) }}
                </span>
            </li>
            @if($status === 'approved')
                <li style="margin-bottom: 8px;"><strong>Nova Entrada:</strong> {{ $dados['entrada'] }}</li>
                <li style="margin-bottom: 8px;"><strong>Nova Saída:</strong> {{ $dados['saida'] }}</li>
            @endif
        </ul>
    </div>

    <p>Podes verificar os teus registos atualizados no painel do sistema.</p>
    
    <p style="font-size: 12px; color: #9ca3af; margin-top: 40px;">Este e-mail foi gerado automaticamente pelo Sistema Timely.</p>
</div>