<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MqttTemperatureQueryController extends Controller {
    public function getMQTTdata()
    {
        $server   = '192.168.87.99';
        $port     = 1883;
        $clientId = 'test-subscriber';

        $mqtt = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);
        $mqtt->connect();
        $mqtt->subscribe('homeassistant/home/workroom/temperature', function ($topic, $message, $retained, $matchedWildcards) {
            echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);
        }, 0);
        $mqtt->loop(true);
        $mqtt->disconnect();
    }
}