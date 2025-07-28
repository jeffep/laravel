<?php

namespace App\Http\Controllers;

use App\Models\AutomationRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use SplObjectStorage;

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
    public $topic; // Fixed missing semicolon
    public $name; // Added missing property

    public function __construct($id, $topic)
    {
        $this->id = $id;
        $this->topic = $topic; // Fixed assignment
        $this->name = $topic; // Adjust if name differs
    }
}

class AutomationEntry
{
    public $id;
    public $testDeviceStorage;
    public $resultDeviceStorage;
    public $device; // Added for _TEST
    public $test; // Added for _RESULT
    public $value; // Added for _VALUE
    public $device2; // Added for _DEVICE02
    public $setting; // Added for _SETTING02
    public $device3; // Added for _DEVICE03
    public $setting2; // Added for _SETTING03
    public $active; // Added for toggle functionality

    public function __construct($id)
    {
        $this->id = $id;
        $this->testDeviceStorage = new SplObjectStorage();
        $this->resultDeviceStorage = new SplObjectStorage();
        $this->active = true; // Default to active
    }
}

class AutomationController extends Controller
{
    protected $shellyDeviceStorage;
    protected $temperatureDeviceStorage;
    protected $automationStorage;

    public function __construct()
    {
        $this->shellyDeviceStorage = new SplObjectStorage();
        $this->temperatureDeviceStorage = new SplObjectStorage();
        $this->automationStorage = new SplObjectStorage();

        // Load Shelly devices from .env
        foreach ($_ENV as $key => $value) {
            if (strpos($key, 'SHELLY') === 0) {
                $shellyId = substr($key, 0, 8);
                $foundShellyDevice = $this->findShellyDevice($shellyId);
                if ($foundShellyDevice === null) {
                    $newDevice = new ShellyDevice($shellyId);
                    $this->shellyDeviceStorage->attach($newDevice);
                    $foundShellyDevice = $newDevice;
                }
                if (strpos($key, '_NAME') !== false) {
                    $foundShellyDevice->name = $value;
                    \Log::info('Stored ' . $value . ' in ' . $foundShellyDevice->id);
                } elseif (strpos($key, '_ADDRESS') !== false) {
                    $foundShellyDevice->address = $value;
                    \Log::info('Stored ' . $value . ' in ' . $foundShellyDevice->id);
                } elseif (strpos($key, '_STATUS') !== false) {
                    $foundShellyDevice->statusCommand = $value;
                    \Log::info('Stored ' . $value . ' in ' . $foundShellyDevice->id);
                }
            }
        }
        foreach ($this->shellyDeviceStorage as $object) {
            \Log::info('Loaded: ' . $object->id . ',' . $object->name . ',' . $object->address);
        }

        // Load Temperature devices
        foreach ($_ENV as $key => $value) {
            if (strpos($key, 'TEMPERATURE') === 0) {
                $temperatureId = substr($key, 0, 11);
                $foundTemperatureDevice = $this->findTemperatureDevice($temperatureId);
                if ($foundTemperatureDevice === null) {
                    $newDevice = new TemperatureDevice($temperatureId, $value);
                    $this->temperatureDeviceStorage->attach($newDevice);
                    $foundTemperatureDevice = $newDevice;
                }
                if (strpos($key, '_TOPIC') !== false) {
                    $foundTemperatureDevice->topic = $value;
                    \Log::info('Stored ' . $value . ' in ' . $foundTemperatureDevice->id);
                }
            }
        }
        foreach ($this->temperatureDeviceStorage as $object) {
            \Log::info('Loaded: ' . $object->id . ',' . $object->topic);
        }

        // Load Automations
        foreach ($_ENV as $key => $value) {
            if (strpos($key, 'AUTOMATION') === 0) {
                $automationId = substr($key, 0, 10); // Fixed to match AUTOMATIONXX
                $foundAutomation = $this->findAutomation($automationId);
                if ($foundAutomation === null) {
                    $newAutomation = new AutomationEntry($automationId);
                    $this->automationStorage->attach($newAutomation);
                    $foundAutomation = $newAutomation;
                }
                if (strpos($key, '_TEST') !== false) {
                    $foundAutomation->device = $value;
                    \Log::info('Stored test ' . $value . ' in ' . $foundAutomation->id);
                } elseif (strpos($key, '_RESULT') !== false) {
                    $foundAutomation->test = $value;
                    \Log::info('Stored result ' . $value . ' in ' . $foundAutomation->id);
                } elseif (strpos($key, '_VALUE') !== false) {
                    $foundAutomation->value = $value;
                    \Log::info('Stored value ' . $value . ' in ' . $foundAutomation->id);
                } elseif (strpos($key, '_DEVICE02') !== false) {
                    $foundAutomation->device2 = $value;
                    \Log::info('Stored device2 ' . $value . ' in ' . $foundAutomation->id);
                } elseif (strpos($key, '_SETTING02') !== false) {
                    $foundAutomation->setting = $value;
                    \Log::info('Stored setting ' . $value . ' in ' . $foundAutomation->id);
                } elseif (strpos($key, '_DEVICE03') !== false) {
                    $foundAutomation->device3 = $value;
                    \Log::info('Stored device3 ' . $value . ' in ' . $foundAutomation->id);
                } elseif (strpos($key, '_SETTING03') !== false) {
                    $foundAutomation->setting2 = $value;
                    \Log::info('Stored setting2 ' . $value . ' in ' . $foundAutomation->id);
                } elseif (strpos($key, '_ACTIVE') !== false) {
                    $foundAutomation->active = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    \Log::info('Stored active ' . $value . ' in ' . $foundAutomation->id);
                }
            }
        }
        foreach ($this->automationStorage as $object) {
            \Log::info('Loaded: ' . $object->id . ',' . $object->device . ',' . $object->test . ',' . $object->value);
        }
    }

    protected function findShellyDevice($id)
    {
        foreach ($this->shellyDeviceStorage as $device) {
            if ($device->id === $id) {
                return $device;
            }
        }
        return null;
    }

    protected function findTemperatureDevice($id)
    {
        foreach ($this->temperatureDeviceStorage as $device) {
            if ($device->id === $id) {
                return $device;
            }
        }
        return null;
    }

    protected function findAutomation($id)
    {
        foreach ($this->automationStorage as $automation) {
            if ($automation->id === $id) {
                return $automation;
            }
        }
        return null;
    }

    public function index()
    {
        $automationSettings = AutomationRule::all(); // Load from database
        \Log::info('Automation Settings:', ['settings' => $automationSettings->toArray()]);
        return view('automation', compact('automationSettings'));
    }

    public function store(Request $request)
    {
        return $this->automation_store($request); // Delegate to existing method
    }

    public function automation_store(Request $request)
    {
        $shelly_ip = "192.168.87.22"; // Replace with dynamic IP if needed
        $status = $request->input('status') == 'on' ? 'on' : 'off';

        try {
            $response = Http::get("http://$shelly_ip/relay/0?turn=$status");
            \Log::info('Shelly response: ' . $response->body());

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

    public function toggleActive(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:automation_rules,id',
            'active' => 'required|boolean',
        ]);

        try {
            $rule = AutomationRule::findOrFail($request->id);
            $rule->active = $request->active;
            $rule->save();

            return response()->json(['message' => 'Rule updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update rule: ' . $e->getMessage()], 500);
        }
    }
}
