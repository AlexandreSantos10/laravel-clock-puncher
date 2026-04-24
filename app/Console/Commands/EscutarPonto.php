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
    protected $signature = 'mqtt:listen';
    protected $description = 'Subscribes to MQTT topics from the ESP32 and registers attendance and enrollments';

    public function handle()
    {
        $this->info('Starting connection to the MQTT broker...');

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
            
        $mqtt = new MqttClient(config('mqtt.host'), (int) config('mqtt.port'), 'laravel-attendance-worker');
       
        $mqtt->connect($settings, false);

        $this->info('On! Listening to the topics...');

        // --- Attendance Logic (Clock-in/out) ---
        $mqtt->subscribe('Ponto/FingerID', function (string $topic, string $message) use ($mqtt) {
            try {
                $date = Carbon::now()->format('Y-m-d');
                $time = Carbon::now()->format('H:i');

                $this->info("Received ID: " . $message . " | Generated: " . $date . " " . $time);
                $this->registarPonto($message, $date, $time, $mqtt);

            } catch (\Throwable $e) {
                $this->error("Error processing attendance: " . $e->getMessage());
            }
        }, 0);

        
        $mqtt->subscribe('Enroll/Response', function (string $topic, string $message) {
            $this->info("--- Enroll event detected ---");
            
            $userId = Cache::get('active_enrollment_id');

            if ($userId) {
                $this->info("Result received from sensor: " . $message . " for User ID: " . $userId);
                
                if ($message === "1") {
                    $user = User::find($userId);
                    if ($user) {
                        $user->update(['finger' => true]);
                        $this->info("Database updated: Biometry Active for {$user->name}");
                    }
                }
                Cache::put("enroll_status_{$userId}", $message, 30);
                
            } else {
                $this->error("Received '{$message}' from sensor, but there is no active enrollment request on the Dashboard.");
            }
        }, 0);

        $mqtt->loop(true); 
    }

    public function registarPonto($user_id, $data, $hora, $mqtt)
    {
        $user = User::find($user_id);

        if (!$user) {
            $this->error('User not found: ' . $user_id);
            $mqtt->publish('Ponto/Response', 'Error: User Not Found', 0, false);
            return;
        }

        $log = Logs::where('user_id', $user->id)->where('data', $data)->first();

        if (!$log) {
            // Logic for a new clock-in entry
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
            $this->info('Clock-in registered for: ' . $user->name);

        } else if (!$log->saida || $log->saida === '00:00' || $log->saida === '00:00:00') {
            
            // Logic for clock-out entry
            $horaEntrada = Carbon::parse($log->entrada);
            $horaSaida = Carbon::parse($hora);
            $minutosTrabalhados = $horaEntrada->diffInMinutes($horaSaida);
            
            // Deducting lunch hour if worked more than 60 mins
            if ($minutosTrabalhados > 60) {
                $minutosTrabalhados -= 60;
            }
            
            $totalHorasCalculado = sprintf('%02d:%02d:00', intdiv($minutosTrabalhados, 60), $minutosTrabalhados % 60);
            
            $log->update([
                'saida'       => $hora,
                'total_horas' => $totalHorasCalculado,
                'updated_by'  => "Laravel System",
            ]);

            $this->info('Clock-out registered for: ' . $user->name . ' | Total Time: ' . $totalHorasCalculado);
        }

        $mqtt->publish('Ponto/Response', $user->name, 0, false);
    }
}