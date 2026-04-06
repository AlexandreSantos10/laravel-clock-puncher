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
    protected $description = 'Subscreve os tópicos MQTT do ESP32 e regista os pontos';

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

        $mqtt->subscribe('ponto/fingerid', function (string $topic, string $message) {
            Cache::put('mqtt_temp_id', $message, 60);
            $this->info("Recebido ID: " . $message);
        }, 0);

        $mqtt->subscribe('ponto/date', function (string $topic, string $message) {
            Cache::put('mqtt_temp_date', $message, 60);
            $this->info("Recebida Data: " . $message);
        }, 0);

        $mqtt->subscribe('ponto/time', function (string $topic, string $message) use ($mqtt) {
            try {
                $id = Cache::get('mqtt_temp_id');
                $date = Cache::get('mqtt_temp_date');
                $time = $message;

                $this->info("Recebida Hora: " . $time);

                if ($id && $date && $time) {
                    $this->registarPonto($id, $date, $time, $mqtt);
                    
                    Cache::forget('mqtt_temp_id');
                    Cache::forget('mqtt_temp_date');
                } else {
                    $this->error("Aviso: Dados incompletos. Faltou receber algum tópico.");
                }
            } catch (\Throwable $e) {
                $this->error("ERRO CRÍTICO DETETADO: " . $e->getMessage());
                $this->error("No ficheiro: " . $e->getFile() . " (Linha: " . $e->getLine() . ")");
            }
        }, 0);

        $mqtt->subscribe('enroll/response', function (string $topic, string $message) {
            $this->info("Recebida resposta de enroll: " . $message);
        }, 0);

        $mqtt->loop(true); 
    }

    public function registarPonto($user_id, $data, $hora, $mqtt)
    {
        $this->info("Entrou na função de registo!");
        
        $user = User::find($user_id);

        if (!$user) {
            $this->error('Utilizador não encontrado: ' . $user_id);
            $mqtt->publish('ponto/response', 'Erro: User Nao Encontrado', 0, false);
            return;
        }

        $log = Logs::where('user_id', $user->id)->where('data', $data)->first();

        if (!$log) {
            $this->info("A criar nova entrada...");
            
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
                'updated_by'    => "Not Updated",
            ]);
            $this->info('Entrada registada: ' . $user->name);

        } else if (!$log->saida || $log->saida === '00:00' || $log->saida === '00:00:00') {
            
            $this->info("A registar saída...");
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
                'updated_by'   => "Laravel System",
            ]);

            $this->info('Saída registada: ' . $user->name . ' | Total Horas: ' . $totalHorasCalculado);
        }

        $mqtt->publish('ponto/response', $user->name, 0, false);
    }
}