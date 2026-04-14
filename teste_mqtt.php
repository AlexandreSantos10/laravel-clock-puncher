<?php

require __DIR__ . '/vendor/autoload.php';

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host     = $_ENV['MQTT_HOST'];
$port     = (int) $_ENV['MQTT_PORT'];
$username = $_ENV['MQTT_USERNAME'];
$password = $_ENV['MQTT_PASSWORD'];

echo "A ligar a: $host:$port\n";

$settings = (new ConnectionSettings)
    ->setUseTls(true)
    ->setTlsVerifyPeer(false)
    ->setUsername($username)
    ->setPassword($password)
    ->setKeepAliveInterval(60)
    ->setConnectTimeout(10);

$mqtt = new MqttClient($host, $port, 'laravel-teste-123');
$mqtt->connect($settings, true);

echo "Ligado!\n";

$mqtt->publish('empresa/teste', 'hello from laravel');

echo "Mensagem enviada!\n";

$mqtt->disconnect();