<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;

class DeviceSeeder extends Seeder
{
    public function run()
    {
        $devices = [
            [
                'name' => 'Den Blower',
                'address' => '192.168.87.22',
                'status_endpoint' => 'status',
                'report_url' => 'http://192.168.87.22/status',
                'action_on' => 'http://192.168.87.22/relay/0 turn=on',
                'action_off' => 'http://192.168.87.22/relay/0 turn=off',
            ],
            [
                'name' => 'Den Water Pump',
                'address' => '192.168.87.21',
                'status_endpoint' => 'rpc/Shelly.GetStatus',
                'report_url' => 'http://192.168.87.21/rpc/Shelly.GetStatus',
                'action_on' => 'http://192.168.87.21/relay/0 turn=on',
                'action_off' => 'http://192.168.87.21/relay/0 turn=off',
            ],
                        [
                'name' => 'Bedroom Blower',
                'address' => '192.168.87.26',
                'status_endpoint' => 'status',
                'report_url' => 'http://192.168.87.26/status',
                'action_on' => 'http://192.168.87.26/relay/0 turn=on',
                'action_off' => 'http://192.168.87.26/relay/0 turn=off',
            ],
                        [
                'name' => 'Bedroom Water Pump',
                'address' => '192.168.87.28',
                'status_endpoint' => 'rpc/Shelly.GetStatus',
                'report_url' => 'http://192.168.87.28/rpc/Shelly.GetStatus',
                'action_on' => 'http://192.168.87.28/relay/0 turn=on',
                'action_off' => 'http://192.168.87.28/relay/0 turn=off',
            ],
                        [
                'name' => 'Den Ceiling Fan',
                'address' => '192.168.87.23',
                'status_endpoint' => 'rpc/Shelly.GetStatus',
                'report_url' => 'http://192.168.87.23/status',
                'action_on' => 'http://192.168.87.23/relay/0 turn=on',
                'action_off' => 'http://192.168.87.23/relay/0 turn=off',
            ],
                       [
                'name' => 'Bedroom Heater',
                'address' => '192.168.87.27',
                'status_endpoint' => 'rpc/Shelly.GetStatus',
                'report_url' => 'http://192.168.87.27/rpc/Shelly.GetStatus',
                'action_on' => 'http://192.168.87.27/relay/0 turn=on',
                'action_off' => 'http://192.168.87.27/relay/0 turn=off',
            ],
        ];

        foreach ($devices as $device) {
            Device::create($device);
        }
    }
}
