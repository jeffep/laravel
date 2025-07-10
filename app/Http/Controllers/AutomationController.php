<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use SplObjectStorage;
//use ShellyDevice;

class ShellyDevice
{
    public $id;
    public $name;
    public $address;
    public $statusCommand;

    public function __construct($id, $name = null, $address = null, $statusCommand = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->statusCommand = $statusCommand;
    }
}
class TemperatureDevice
{
   public $id;
   public $topic

   public function __construct($id, $topic)
   {
      $this->id = $id;
      $this->name = $name;
   }
}

class AutomationDevice
{
   public $id;
   public $name;
   public $address[];
   public action[];
   public result[];

   public function __construct($id, $name)
   {
     $this->id = $id;
     $this->name = $name;
   }
}

class AutomationEntry
{
   public $testDeviceStorage;
   public $resultDeviceStorage;
   public $id;

   public function __construct($id)
   {
     $this->id = $id;
     $this->testDeviceStorage = new SplObjectStorage();
     $this->resultDeviceStorage = new SplObjectStorage();
   }
}

class AutomationController extends Controller
{

   // Initialize the Shelly information storage
   protected $shellyDeviceStorage;  // = new SplObjectStorage();
   protected $temperatureDeviceStorage;
   protected $automationStorage;


   public function __construct()
   {

       $this->shellyDeviceStorage = new SplObjectStorage();
       $this->temperatureDeviceStorage = new SplObjectStorage();
       $this->automationStorage = new SplObjectStorage();


       // FIRST Load Shelly IPs from .env and populate the array
       foreach ($_ENV as $key => $value) {
           if (strpos($key, 'SHELLY') === 0) {
                   // Extract the custom identifier (e.g., SHELLY01)
                   $shellyId = substr($key, 0, 8);
                   $foundShellyDevice = $this->findShellyDevice($shellyId);
                   if ($foundShellyDevice === null) {
                       $newDevice = new ShellyDevice($shellyId);
                       $this->shellyDeviceStorage->attach($newDevice);
                       $foundShellyDevice = $newDevice;
                   }
                   // Check if the key represents the name or address
                   if (strpos($key, '_NAME') !== false) {
                       // Store the name
                       $foundShellyDevice->name = $value;
                       \Log::info('stored ' . $value . ' in ' . $foundShellyDevice->id);
                   } elseif (strpos($key, '_ADDRESS') !== false) {
                       // Store the address
                       $foundShellyDevice->address = $value;
                       \Log::info('stored ' . $value . 'in ' . $foundShellyDevice->id);
                   } elseif (strpos($key, '_STATUS') !== false) {
                       // Store the status command
                       $foundShellyDevice->statusCommand = $value;
                       \Log::info('stored ' . $value . 'in ' . $foundShellyDevice->id);
                  }
           }
       }
       // Now loop through the array and log the loaded data
              //$keys = array_keys($this->shellyInfo);
       foreach ($this->shellyDeviceStorage as $object) {
              \Log::info('Loaded: ' . $object->id . ',' . $object->name . ',' . $object->address);
       }

       // SECOND Load the Temperature devices

       foreach ($_ENV as $key => $value) {
           if (strpos($key, 'TEMPERATURE') === 0) {
                   // Extract the custom identifier (e.g., TEMPERATURE01)
                   $temperatureId = substr($key, 0, 11);
                   $foundTemperatureDevice = $this->findTemperatureDevice($temperatureId);
                   if ($foundTemperatureDevice === null) {
                       $newDevice = new TemperatureDevice($temperatureId);
                       $this->TemperatureDeviceStorage->attach($newDevice);
                       $foundTemperatureDevice = $newDevice;
                   }
                   // Check if the key represents the name or address
                   if (strpos($key, '_TOPIC') !== false) {
                       // Store the name
                       $foundShellyDevice->topic = $value;
                       \Log::info('stored ' . $value . ' in ' . $foundTemperatureDevice->id);
                  }
           }
       }
       // Now loop through the array and log the loaded data
       foreach ($this->TemperatureDeviceStorage as $object) {
              \Log::info('Loaded: ' . $object->id . ',' . $object->name . ',' . $object->address);
       }


       // THIRD Load the Automations (if any)
       foreach ($_ENV as $key => $value) {
           if (strpos($key, 'AUTOMATION') === 0) {
                   // Extract the custom identifier (e.g., AUTOMATION01)
                   $automationId = substr($key, 0, 12);
                   $foundAutomation = $this->findAutomation($automationId);
                   if ($foundAutomation === null) {
                       $newDevice = new AutomationEntry($automationId);
                       $this->automationStorage->attach($newDevice);
                       $foundAutomation = $newDevice;
                   }
                   // Check if the key represents the name or address
                   if (strpos($key, '_TEST') !== false) {
                       // Store which Device
                       $foundAutomation->device = $value;
                       \Log::info('stored ' . $value . ' in ' . $foundAutomation->device);
                   } elseif (strpos($key, '_RESULT') !== false) {
                       // Store the test
                       $foundAutomation->test = $value;
                       \Log::info('stored ' . $value . 'in ' . $foundAutomation->device);
                   } elseif (strpos($key, '_VALUE') !== false) {
                       // Store the value
                       $foundAutomation->value = $value;
                       \Log::info('stored ' . $value . 'in ' . $foundAutomation->device);
                   } elseif (strpos($key, '_DEVICE02') !== false) {
                       // Store the value
                       $foundAutomation->value = $value;
                       \Log::info('stored ' . $value . 'in ' . $foundAutomation->device);
                   } elseif (strpos($key, '_SETTING02') !== false) {
                       // Store the value
                       $foundAutomation->value = $value;
                       \Log::info('stored ' . $value . 'in ' . $foundAutomation->device);
                   } elseif (strpos($key, '_DEVICE03') !== false) {
                       // Store the value
                       $foundAutomation->value = $value;
                       \Log::info('stored ' . $value . 'in ' . $foundAutomation->device);
                   } elseif (strpos($key, '_SETTING03') !== false) {
                       // Store the value
                       $foundAutomation->value = $value;
                       \Log::info('stored ' . $value . 'in ' . $foundAutomation->device);
                  
           }
       }
       // Now loop through the array and log the loaded data
              //$keys = array_keys($this->shellyInfo);
       foreach ($this->shellyDeviceStorage as $object) {
              \Log::info('Loaded: ' . $object->id . ',' . $object->name . ',' . $object->address);
       }


   }


    public function automation_store(Request $request)
    {
       $shelly_ip = "192.168.87.22"; // Replace with your Shelly device IP
       $status = $request->input('status') == 'on' ? 'on' : 'off';
   
       try {
           // Send command to Shelly device
           $response = Http::get("http://$shelly_ip/relay/0?turn=$status");

           \Log::info('Shelly response: ' . $response->body());

           // Check if the response was successful
           if ($response->successful()) {
               $newStatus = $status == 'on' ? 'on' : 'off';
               session(['status' => $newStatus]);
               return redirect()->back()->with('status', 'Air conditioner turned ' . $newStatus);
           } else {
               \Log::error('Shelly API request failed.');
               return redirect()->back()->with('error', 'Failed to toggle the device.');
           }
       } catch (\Exception $e) {
           \Log::error('Exception: ' . $e->getMessage());
           return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
       }
   }

}
