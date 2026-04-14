<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Logs;
use Carbon\Carbon;

class EscutarPonto extends Command
{
    protected $signature = 'mqtt:escutar';
    protected $description = 'Subscreve os tópicos MQTT do ESP32 e regista os pontos e matrículas';

    public function handle()
    {
        $this->info('A iniciar ligação ao broker MQTT...');

        $settings = (new ConnectionSettings)
            ->setUseTls(true)
            ->setTlsVerifyPeer(false)
            ->setUsername(config('mqtt.username'))
            ->setPassword(config('mqtt.password'))
            ->setKeepAliveInterval(60)
            ->setConnectTimeout(10)
            ->setReconnectAutomatically(true) 
            ->setDelayBetweenReconnectAttempts(5) 
            ->setMaxReconnectAttempts(100);
            
        $mqtt = new MqttClient(config('mqtt.host'), (int) config('mqtt.port'), 'laravel-ponto-worker');
       
        $mqtt->connect($settings, false);

        $this->info('Ligado! A escutar tópicos...');

        $mqtt->subscribe('Ponto/FingerID', function (string $topic, string $message) use ($mqtt) {
            try {
                $date = Carbon::now()->format('Y-m-d');
                $time = Carbon::now()->format('H:i');

                $this->info("Recebido ID: " . $message . " | Gerado: " . $date . " " . $time);
                $this->registarPonto($message, $date, $time, $mqtt);

            } catch (\Throwable $e) {
                $this->error("Erro no processamento do ponto: " . $e->getMessage());
            }
        }, 0);

        $mqtt->subscribe('Enroll/Response', function (string $topic, string $message) {
            $this->info("--- EVENTO DE ENROLL DETETADO ---");
            
            $userId = Cache::get('active_enrollment_id');

            if ($userId) {
                $this->info("Resultado recebido do sensor: " . $message . " para o User ID: " . $userId);
                if ($message === "1") {
                    $user = User::find($userId);
                    if ($user) {
                        $user->update(['finger' => true]);
                        $this->info("Base de Dados atualizada: Biometria Ativa para {$user->name}");
                    }
                }
                Cache::put("enroll_status_{$userId}", $message, 30);
                
            } else {
                $this->error("Recebi '{$message}' do sensor, mas não há nenhum pedido de matrícula ativo no Dashboard.");
            }
        }, 0);

        $mqtt->loop(true); 
    }

    public function registarPonto($user_id, $data, $hora, $mqtt)
    {
        $user = User::find($user_id);

        if (!$user) {
            $this->error('Utilizador não encontrado: ' . $user_id);
            $mqtt->publish('Ponto/Response', 'Erro: User Nao Encontrado', 0, false);
            return;
        }

        $log = Logs::where('user_id', $user->id)->where('data', $data)->first();

        if (!$log) {
            $fimAlmoco = $user->inicio_almoco 
                ? Carbon::parse($user->inicio_almoco)->addHour()->format('H:i') 
                : '14:00';

            Logs::create([
                'user_id'      => $user->id,
                'data'         => $data,
                'entrada'      => $hora,
                'final_almoço' => $fimAlmoco,
                'saida'        => "00:00", 
                'total_horas'  => "00:00",
                'obs'          => "Automatic Log",
                'created_by'   => "Laravel System",
                'updated_by'   => "Not Updated",
            ]);
            $this->info('Entrada registada para: ' . $user->name);

        } else if (!$log->saida || $log->saida === '00:00' || $log->saida === '00:00:00') {
            
            $horaEntrada = Carbon::parse($log->entrada);
            $horaSaida = Carbon::parse($hora);
            $minutosTrabalhados = $horaEntrada->diffInMinutes($horaSaida);
            
            if ($minutosTrabalhados > 60) {
                $minutosTrabalhados -= 60;
            }
            
            $totalHorasCalculado = sprintf('%02d:%02d:00', intdiv($minutosTrabalhados, 60), $minutosTrabalhados % 60);
            
            $log->update([
                'saida'       => $hora,
                'total_horas' => $totalHorasCalculado,
                'updated_by'  => "Laravel System",
            ]);

            $this->info('Saída registada para: ' . $user->name . ' | Total: ' . $totalHorasCalculado);
        }

        $mqtt->publish('Ponto/Response', $user->name, 0, false);
    }
}