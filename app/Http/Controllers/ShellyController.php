<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Device;

class ShellyController extends Controller
{
    private function getShellyStatus($shellyIp, $statusEndpoint)
    {
        if (env('SHELLY_FAKE', false)) {
            Log::info("Faux Shelly status: ip=$shellyIp, endpoint=$statusEndpoint");
            return [
                'relay' => ['ison' => false, 'power' => 0],
                'meters' => [['power' => 0]]
            ];
        }

        $url = "http://$shellyIp/$statusEndpoint";
        try {
            Log::info('Trying ' . $url);
            $response = Http::get($url);

            if ($response->successful()) {
                Log::info('Shelly response: ' . $response);
                return $response->json();
            } else {
                Log::info('Shelly response: ' . $response);
                return ['error' => 'Failed to retrieve status'];
            }
        } catch (\Exception $e) {
            Log::error('Exception: ' . $e->getMessage());
            return ['error' => 'An error occurred: ' . $e->getMessage()];
        }
    }

    private function getShellyWattage($shellyIp)
    {
        if (env('SHELLY_FAKE', false)) {
            Log::info("Faux Shelly wattage: ip=$shellyIp");
            return 100; // Mock wattage for testing
        }

        $wattage = 0;
        $attempts = 0;
        $maxAttempts = 10;
        $delay = 5;

        while ($wattage == 0 && $attempts < $maxAttempts) {
            $response = Http::get("http://$shellyIp/status");
            if ($response->successful()) {
                $responseData = $response->json();
                $wattage = $responseData['meters'][0]['power'];
            }
            if ($wattage == 0) {
                sleep($delay);
                $attempts++;
            }
        }

        return $wattage;
    }

    private function logWattage($shellyId, $shellyIp, $status, $wattage)
    {
        $wattageLogFile = storage_path('app/public/wattage.log');
        $logEntry = [
            'timestamp' => time(),
            'device' => $shellyId,
            'action' => $status,
            'wattage' => $wattage
        ];
        file_put_contents($wattageLogFile, json_encode($logEntry) . PHP_EOL, FILE_APPEND);
    }

    public function shelly_status()
    {
        $shellyStatus = [];
        $devices = Device::all();

        foreach ($devices as $device) {
            Log::info('Getting status of: ' . $device->address);
            $status = $this->getShellyStatus($device->address, $device->status_endpoint);
            $statusType = ($device->status_endpoint == 'status') ? 1 : (($device->status_endpoint == 'rpc/Shelly.GetStatus') ? 2 : 0);
            $shellyStatus[] = [
                'id' => $device->name,
                'ip' => $device->address,
                'statusType' => $statusType,
                'status' => $status,
            ];
        }

        return view('shelly_status', ['shellyStatus' => $shellyStatus]);
    }

    public function toggle(Request $request)
    {
        $shellyId = $request->input('shelly_id'); // Device name, e.g., "Den Blower"
        $shellyIp = $request->input('shelly_ip');
        $status = $request->input('status') ? 'on' : 'off';

        $device = Device::where('name', $shellyId)->first();
        if (!$device) {
            Log::error("Device with name $shellyId not found.");
            return redirect()->back()->with('error', 'Device not found.');
        }

        $actionUrl = $status === 'on' ? $device->action_on : $device->action_off;
        Log::info('Sending to shelly: ' . $actionUrl);

        if (env('SHELLY_FAKE', false)) {
            Log::info("Faux Shelly RPC: url=$actionUrl");
            $responseBody = ['ison' => $status === 'on'];
        } else {
            $response = Http::get($actionUrl);
            Log::info('Shelly response: ' . $response->body());
            $responseBody = $response->json() ?? [];
        }

        $updatedStatus = 'off';
        try {
            if (env('SHELLY_FAKE', false)) {
                Log::info("Faux Shelly status check: ip=$shellyIp");
                $responseData = ['ison' => $status === 'on'];
            } else {
                $response = Http::get("http://$shellyIp/relay/0");
                $responseData = $response->successful() ? $response->json() : [];
            }

            $updatedStatus = isset($responseData['ison']) && $responseData['ison'] ? 'on' : 'off';
        } catch (\Exception $e) {
            Log::error('Exception while fetching updated Shelly status: ' . $e->getMessage());
        }

        $wattage = $status !== 'on' ? 0 : $this->getShellyWattage($shellyIp);
        $this->logWattage($shellyId, $shellyIp, $status, $wattage);

        session()->flash('shelly_status', $updatedStatus);

        return redirect()->back();
    }

    public function toggle2(Request $request)
    {
        $shellyId = $request->input('shelly_id'); // Device name, e.g., "Den Blower"
        $shellyIp = $request->input('shelly_ip');
        $status = $request->input('status') ? 'on' : 'off';

        $device = Device::where('name', $shellyId)->first();
        if (!$device) {
            Log::error("Device with name $shellyId not found.");
            return redirect()->back()->with('error', 'Device not found.');
        }

        $actionUrl = $status === 'on' ? $device->action_on : $device->action_off;
        Log::info('Sending to shelly: ' . $actionUrl);

        if (env('SHELLY_FAKE', false)) {
            Log::info("Faux Shelly RPC: url=$actionUrl");
            $responseBody = ['ison' => $status === 'on'];
        } else {
            $response = Http::get($actionUrl);
            Log::info('Shelly response: ' . $response->body());
            $responseBody = $response->json() ?? [];
        }

        $updatedStatus = 'off';
        try {
            if (env('SHELLY_FAKE', false)) {
                Log::info("Faux Shelly status check: ip=$shellyIp");
                $responseData = ['ison' => $status === 'on'];
            } else {
                $response = Http::get("http://$shellyIp/relay/0");
                $responseData = $response->successful() ? $response->json() : [];
            }

            $updatedStatus = isset($responseData['ison']) && $responseData['ison'] ? 'on' : 'off';
        } catch (\Exception $e) {
            Log::error('Exception while fetching updated Shelly status: ' . $e->getMessage());
        }

        session()->flash('shelly_status', $updatedStatus);

        return redirect()->back();
    }

    public function showShellyDevice()
    {
        $device = Device::find(5); // Den Ceiling Fan
        return $this->renderDeviceView($device, 'Den Fan');
    }

    public function showShellyDevice2()
    {
        $device = Device::find(6); // Bedroom Heater
        return $this->renderDeviceView($device, 'Bedroom Heater');
    }

    public function showShellyDevice3()
    {
        $device = Device::find(4); // Bedroom Water Pump
        return $this->renderDeviceView($device, 'Bedroom Water Pump');
    }

    public function showShellyDevice4()
    {
        $device = Device::find(3); // Bedroom Blower
        return $this->renderDeviceView($device, 'Bedroom Blower');
    }

    public function showShellyLight1()
    {
        $device = Device::find(7); // Breakfast Light
        return $this->renderDeviceView($device, 'Breakfast Light');
    }

    public function showShellyLight2()
    {
        $device = Device::find(8); // Camper Light
        return $this->renderDeviceView($device, 'Camper Light');
    }

    public function showShellyLight3()
    {
        $device = Device::find(9); // Garage Light
        return $this->renderDeviceView($device, 'Garage Light');
    }

    public function showShellyLight4()
    {
        $device = Device::find(10); // Lamp Post Light
        return $this->renderDeviceView($device, 'Lamp Post Light');
    }

    public function showShellyLight5()
    {
        $device = Device::find(11); // Foyer Light
        return $this->renderDeviceView($device, 'Foyer Light');
    }

    public function showShellyLight6()
    {
        $device = Device::find(12); // Front Porch Light
        return $this->renderDeviceView($device, 'Front Porch Light');
    }

    public function showDevice($id)
    {
        $deviceIdMap = [
            1 => 5, // Den Fan
            2 => 6, // Bedroom Heater
            3 => 4, // Bedroom Water Pump
            4 => 3, // Bedroom Blower
        ];
        $deviceNames = [
            1 => 'Den Fan',
            2 => 'Bedroom Heater',
            3 => 'Bedroom Water Pump',
            4 => 'Bedroom Blower',
        ];
        $deviceId = $deviceIdMap[$id] ?? null;
        $deviceName = $deviceNames[$id] ?? 'Unknown Device';
        $device = $deviceId ? Device::find($deviceId) : null;
        $status = $device ? $this->getShellyStatus($device->address, $device->status_endpoint) : null;
        return view('shellyDevice', [
            'ip_address' => $device ? $device->address : '',
            'device_name' => $deviceName,
            'status' => $status,
            'error' => $device ? null : "$deviceName not found"
        ]);
    }

    public function showShellyLight($id)
    {
        $deviceIdMap = [
            1 => 7,  // Breakfast Light
            2 => 8,  // Camper Light
            3 => 9,  // Garage Light
            4 => 10, // Lamp Post Light
            5 => 11, // Foyer Light
            6 => 12, // Front Porch Light
        ];
        $deviceNames = [
            1 => 'Breakfast Light',
            2 => 'Camper Light',
            3 => 'Garage Light',
            4 => 'Lamp Post Light',
            5 => 'Foyer Light',
            6 => 'Front Porch Light',
        ];
        $deviceId = $deviceIdMap[$id] ?? null;
        $deviceName = $deviceNames[$id] ?? 'Unknown Light';
        $device = $deviceId ? Device::find($deviceId) : null;
        return $this->renderDeviceView($device, $deviceName);
    }

    private function renderDeviceView($device, $deviceName)
    {
        if ($device) {
            return view('shellyDevice', ['ip_address' => $device->address]);
        }

        Log::error("Shelly device $deviceName (ID {$device->id}) not found.");
        return view('shellyDevice', ['ip_address' => '', 'error' => "$deviceName not found"]);
    }

    // Deprecated methods (consider removing if unused)
    public function shelly_status_old()
    {
        Log::warning('shelly_status_old is deprecated; use shelly_status instead.');
        $shelly_ip = "192.168.87.22";
        $url = "http://$shelly_ip/status";

        try {
            $response = Http::get($url);
            return view('shelly_status', [
                'status' => $response->successful() ? $response->json() : null,
                'error' => $response->successful() ? null : 'Failed to retrieve status.'
            ]);
        } catch (\Exception $e) {
            return view('shelly_status', ['status' => null, 'error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function showStatus()
    {
        Log::warning('showStatus is deprecated; use shelly_status instead.');
        $shelly_ip = "192.168.87.22";
        $status = 'on';

        try {
            $response = Http::get("http://$shelly_ip/status");
            if ($response->successful()) {
                $status = $response->json()['ison'] ? 'on' : 'off';
            } else {
                Log::error('Failed to fetch Shelly status.');
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching Shelly status: ' . $e->getMessage());
        }

        Log::info('Passing status to view: ' . $status);
        return view('shelly_status', ['status' => $status]);
    }

    public function toggleSwitch(Request $request)
    {
        Log::warning('toggleSwitch is deprecated; use toggle or toggle2 instead.');
        $action = $request->input('action');
        $response = Http::post("http://{$this->shellyIp}/relay/0", ['turn' => $action]);
        return redirect()->route('shelly_status');
    }

    public function getStatus($shellyIp)
    {
        Log::warning('getStatus is deprecated; use shelly_status instead.');
        $response = Http::get("http://$shellyIp/status");
        $status = $response->json();
        return view('shelly_status', [
            'status' => isset($status['relay']) ? $status : null,
            'error' => isset($status['relay']) ? null : 'Relay status not found'
        ]);
    }

    public function toggle_old(Request $request)
    {
        Log::warning('toggle_old is deprecated; use toggle or toggle2 instead.');
        $shelly_ip = "192.168.87.22";
        $status = $request->input('status') == 'on' ? 'on' : 'off';

        try {
            $response = Http::get("http://$shelly_ip/relay/0?turn=$status");
            Log::info('Shelly response: ' . $response->body());

            if ($response->successful()) {
                session(['status' => $status]);
                return redirect()->back()->with('status', 'Air conditioner turned ' . $status);
            } else {
                Log::error('Shelly API request failed.');
                return redirect()->back()->with('error', 'Failed to toggle the device.');
            }
        } catch (\Exception $e) {
            Log::error('Exception: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
