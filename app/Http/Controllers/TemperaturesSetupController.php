<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use SplObjectStorage;

class TemperatureSensor {
    public $name;
    public $address;
    public $topic;

    public function __construct($id, $name = null, $address=null, $topic=null) {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->topic = $topic;
    }
}


class TemperaturesSetupController extends Controller
{

 protected $temperatureDeviceStorage;  // = new SplObjectStorage();


public function temperatures_setup()
{
    $this->temperatureDeviceStorage = new SplObjectStorage();
    // Read the file line by line
    $fileLines = file(base_path('temperatures.env'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($fileLines as $line) {
        \Log::info('Line=' . $line);
        // Parse the settings (assuming they are in KEY=VALUE format)
        list($key, $value) = explode('=', $line, 2);

        if (strpos($key, 'TEMPERATURE_SENSOR') === 0) {
            // Extract the custom identifier (e.g., SENSOR01)
            $temperatureId = substr($key, 0, 20);
            $foundTemperatureDevice = $this->findTemperatureDevice($temperatureId);

            if ($foundTemperatureDevice === null) {
                $newDevice = new TemperatureSensor($temperatureId);
                $this->temperatureDeviceStorage->attach($newDevice);
                $foundTemperatureDevice = $newDevice;
            }

            // Check if the key represents the name or address
            if (strpos($key, '_NAME') !== false) {
                // Store the name
                $foundTemperatureDevice->name = $value;
                \Log::info('Stored ' . $value . ' in ' . $foundTemperatureDevice->id);
            } elseif (strpos($key, '_ADDRESS') !== false) {
                // Store the address
                $foundTemperatureDevice->address = $value;
                \Log::info('Stored ' . $value . ' in ' . $foundTemperatureDevice->id);
            } elseif (strpos($key, '_TOPIC') !== false) {
                // Store the topic
                $foundTemperatureDevice->topic = $value;
                \Log::info('Stored ' . $value . ' in ' . $foundTemperatureDevice->id);
            }
        }
    }

    $sensorData = [];
    foreach ($this->temperatureDeviceStorage as $sensor) {
        $sensorData[] = [
            'id' => $sensor->id,
            'name' => $sensor->name,
            'address' => $sensor->address,
            'topic' => $sensor->topic,
        ];
    }


    // Pass $temperatureDeviceStorage to the blade file
    return view('temperatures_setup', ['sensorData' => $sensorData]);
}

function findTemperatureDevice($id)
{
    foreach ($this->temperatureDeviceStorage as $object) {
        if ($object->id === $id) {
            return $object;
        }
    }
    return null; // Not found
}

public function temperatures_save(Request $request)
{
    $key = $request->input('key');
    $value = $request->input('value');

    if (strpos($key, 'SENSOR') === 0) {
        // Extract the custom identifier (e.g., SENSOR01)
        $temperatureId = substr($key, 0, 18);
        $foundTemperatureDevice = $this->findTemperatureDevice($temperatureId);
        if ($foundTemperatureDevice === null) {
            $newDevice = new TemperatureDevice($temperatureId);
            $this->temperatureDeviceStorage->attach($newDevice);
            $foundTemperatureDevice = $newDevice;
        }
        // Check if the key represents the name or address
        if (strpos($key, '_NAME') !== false) {
           // Store the name
           $foundTemperatureDevice->name = $value;
           \Log::info('stored ' . $value . ' in ' . $foundTemperatureDevice->id);
        } elseif (strpos($key, '_ADDRESS') !== false) {
           // Store the address
           $foundTemperatureDevice->address = $value;
           \Log::info('stored ' . $value . 'in ' . $foundTemperatureDevice->id);
        } elseif (strpos($key, '_TOPIC') !== false) {
           // Store the topic
           $foundTemperatureDevice->topic = $value;
           \Log::info('stored ' . $value . 'in ' . $foundTemperatureDevice->id);
        }

    }
    // Append the new setting to the file
    file_put_contents(base_path('temperatures.env'), "\n{$key}={$value}", FILE_APPEND);

    // Redirect back to the settings page
    return redirect()->route('temperatures_setup');
}

}
